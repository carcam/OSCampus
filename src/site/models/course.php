<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

use Oscampus\File;
use Oscampus\Lesson\Properties;
use Oscampus\UserActivity;

defined('_JEXEC') or die();

class OscampusModelCourse extends OscampusModelSite
{
    /**
     * @var UserActivity
     */
    protected $activity = null;

    public function __construct(array $config)
    {
        parent::__construct($config);

        $this->activity = OscampusFactory::getContainer()->activity;
    }

    public function getCourse()
    {
        $db = $this->getDbo();

        $query = $db->getQuery(true)
            ->select(
                array(
                    'course.*',
                    'cp.pathways_id',
                    'pathway.title AS pathway_title'
                )
            )
            ->from('#__oscampus_courses AS course')
            ->innerJoin('#__oscampus_courses_pathways AS cp ON cp.courses_id = course.id')
            ->innerJoin('#__oscampus_pathways AS pathway ON pathway.id = cp.pathways_id')
            ->where(
                array(
                    'course.id = ' . (int)$this->getState('course.id'),
                    'pathway.id = ' . (int)$this->getState('pathway.id'),
                    'course.published = 1',
                    'pathway.published = 1',
                    'course.released <= NOW()'
                )
            );

        $course = $db->setQuery($query)->loadObject();
        if (!$course) {
            throw new Exception(JText::_('COM_OSCAMPUS_ERROR_COURSE_NOT_FOUND', 404));
        }

        return $course;
    }

    /**
     * Teacher information for currently selected course
     *
     * @return object
     */
    public function getTeacher()
    {
        $db  = JFactory::getDbo();
        $cid = (int)$this->getState('course.id');

        $user   = OscampusFactory::getUser();
        $levels = join(',', $user->getAuthorisedViewLevels());

        $query = $db->getQuery(true)
            ->select(
                array(
                    'teacher.*',
                    'user.username',
                    'user.name',
                    'user.email'
                )
            )
            ->from('#__oscampus_teachers AS teacher')
            ->innerJoin('#__oscampus_courses AS course ON course.teachers_id = teacher.id')
            ->leftJoin(('#__users AS user ON user.id = teacher.users_id'))
            ->where('course.id = ' . $cid);

        if ($teacher = $db->setQuery($query)->loadObject()) {
            $teacher->links = json_decode($teacher->links);

            // Get other courses for this teacher
            $queryCourses = $db->getQuery(true)
                ->select('pathway.title pathway_title, course.*')
                ->from('#__oscampus_teachers AS teacher')
                ->innerJoin('#__oscampus_courses AS course ON course.teachers_id = teacher.id')
                ->innerJoin('#__oscampus_courses_pathways AS cp ON cp.courses_id = course.id')
                ->innerJoin('#__oscampus_pathways AS pathway ON pathway.id = cp.pathways_id')
                ->where(
                    array(
                        'course.published = 1',
                        'course.released <= CURDATE()',
                        'pathway.published = 1',
                        'pathway.access IN (' . $levels . ')',
                        'course.id != ' . $cid,
                        'teacher.id = ' . $teacher->id,
                    )
                )
                ->group('course.id')
                ->order('pathway.ordering ASC, cp.ordering ASC, course.title ASC');

            $teacher->courses = $db->setQuery($queryCourses)->loadObjectList();
        }

        return $teacher;
    }

    /**
     * Get all additional file downloads for this course
     *
     * @return File[]
     */
    public function getFiles()
    {
        $files = array();

        $cid = $this->getState('course.id');
        if ($cid > 0) {
            $db = JFactory::getDbo();

            $query = $db->getQuery(true)
                ->select(
                    array(
                        'file.id',
                        'file.path',
                        'file.title',
                        'file.description'
                    )
                )
                ->from('#__oscampus_files AS file')
                ->innerJoin('#__oscampus_files_links AS flink ON flink.files_id = file.id')
                ->innerJoin('#__oscampus_courses AS course ON course.id = flink.courses_id')
                ->leftJoin('#__oscampus_lessons AS lesson ON lesson.id = flink.lessons_id')
                ->leftJoin('#__oscampus_modules AS module ON module.id = lesson.modules_id')
                ->where(
                    array(
                        'flink.courses_id = ' . $cid,
                        'file.published = 1'
                    )
                )
                ->order('module.ordering, lesson.ordering, flink.ordering, file.title')
                ->group('file.id');

            error_reporting(-1);
            ini_set('display_errors', 1);

            $files = $db->setQuery($query)->loadObjectList(null, '\\Oscampus\\File');

            echo $db->getErrorMsg();
            error_reporting(0);
            ini_set('display_errors', 0);


        }
        return $files;
    }

    /**
     * Get lessons for the currently selected course
     *
     * @return array
     */
    public function getLessons()
    {
        $db      = $this->getDbo();
        $cid     = (int)$this->getState('course.id');
        $lessons = array();

        if ($cid > 0) {
            $query = $db->getQuery(true)
                ->select(
                    array(
                        'module.courses_id',
                        'lesson.modules_id',
                        'module.title AS module_title',
                        'lesson.*'
                    )
                )
                ->from('#__oscampus_modules AS module')
                ->innerJoin('#__oscampus_lessons AS lesson ON lesson.modules_id = module.id')
                ->where(
                    array(
                        'module.courses_id = ' . (int)$this->getState('course.id'),
                        'lesson.published = 1'
                    )
                )
                ->order('module.ordering ASC, lesson.ordering ASC');

            $list = $db->setQuery($query)->loadObjectList();

            $module = (object)array(
                'id'      => null,
                'title'   => null,
                'lessons' => array()
            );
            foreach ($list as $index => $lesson) {
                if ($lesson->modules_id != $module->id) {
                    if ($module->lessons) {
                        $lessons[] = clone $module;
                    }
                    $module->id      = $lesson->modules_id;
                    $module->title   = $lesson->module_title;
                    $module->lessons = array();
                }

                $lesson->index     = $index;
                $module->lessons[] = new Properties($lesson);
            }
            if ($module->lessons) {
                $lessons[] = clone $module;
            }
        }

        return $lessons;
    }

    /**
     * Get lesson viewed info for the selected user in the currently selected course
     *
     * @return array
     */
    public function getViewedLessons()
    {
        $uid = (int)$this->getState('user.id');
        $cid = (int)$this->getState('course.id');

        if ($uid > 0 && $cid > 0) {
            $this->activity->setUser($uid);
            return $this->activity->getCourse($cid);
        }

        return array();
    }

    protected function populateState()
    {
        $app = JFactory::getApplication();

        $pid = $app->input->getInt('pid');
        $this->setState('pathway.id', $pid);

        $cid = $app->input->getInt('cid');
        $this->setState('course.id', $cid);

        $uid = $app->input->getInt('uid', JFactory::getUser()->id);
        $this->setState('user.id', $uid);
    }
}

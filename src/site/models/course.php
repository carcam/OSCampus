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
            ->select('c.*, cp.pathways_id, p.title pathway_title')
            ->from('#__oscampus_courses c')
            ->innerJoin('#__oscampus_courses_pathways cp ON cp.courses_id = c.id')
            ->innerJoin('#__oscampus_pathways p ON p.id = cp.pathways_id')
            ->where(
                array(
                    'c.id = ' . (int)$this->getState('course.id'),
                    'p.id = ' . (int)$this->getState('pathway.id')
                )
            );

        $course = $db->setQuery($query)->loadObject();
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

        $query = $db->getQuery(true)
            ->select('i.*, u.username, u.name, u.email')
            ->from('#__oscampus_teachers i')
            ->innerJoin('#__oscampus_courses c ON c.teachers_id = i.id')
            ->leftJoin(('#__users u ON u.id = i.users_id'))
            ->where('c.id = ' . $cid);
        if ($teacher = $db->setQuery($query)->loadObject()) {
            $teacher->links = json_decode($teacher->links);

            // Get other courses for this teacher
            $queryCourses = $db->getQuery(true)
                ->select('p.title pathway_title, c.*')
                ->from('#__oscampus_teachers t')
                ->innerJoin('#__oscampus_courses c ON c.teachers_id = t.id')
                ->innerJoin('#__oscampus_courses_pathways cp ON cp.courses_id = c.id')
                ->innerJoin('#__oscampus_pathways p ON p.id = cp.pathways_id')
                ->where(
                    array(
                        'c.id != ' . $cid,
                        't.id = ' . $teacher->id
                    )
                )
                ->order('p.ordering ASC, cp.ordering ASC, c.title ASC');

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
                ->where('flink.courses_id = ' . $cid)
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
                ->select('m.courses_id, l.modules_id, m.title module_title, l.*')
                ->from('#__oscampus_modules m')
                ->innerJoin('#__oscampus_lessons l ON l.modules_id = m.id')
                ->where(
                    array(
                        'm.courses_id = ' . (int)$this->getState('course.id'),
                        'l.published = 1'
                    )
                )
                ->order('m.ordering ASC, l.ordering ASC');

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

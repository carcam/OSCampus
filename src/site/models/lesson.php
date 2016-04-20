<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

use Oscampus\File;
use Oscampus\Lesson;
use Oscampus\UserActivity;

defined('_JEXEC') or die();

class OscampusModelLesson extends OscampusModelSite
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

    /**
     * @var Lesson
     */
    protected $lesson = null;

    /**
     * @return Lesson
     * @throws Exception
     */
    public function getItem()
    {
        if ($this->lesson === null) {
            $this->lesson = OscampusFactory::getContainer()->lesson;

            if ($lessonId = (int)$this->getState('lesson.id')) {
                $this->lesson->loadById($lessonId);

            } else {
                $courseId = (int)$this->getState('course.id');
                $index    = (int)$this->getState('lesson.index');

                $this->lesson->loadByIndex($index, $courseId);
            }
        }

        if (!$this->lesson->id) {
            throw new Exception(JText::_('COM_OSCAMPUS_ERROR_LESSON_NOT_FOUND', 404));

        } elseif ($uid = (int)$this->getState('user.id')) {
            $this->activity->setUser($uid);
            $this->activity->visitLesson($this->lesson);
        }

        return $this->lesson;
    }

    /**
     * The user activity record for this lesson
     *
     * @return object
     */
    public function getLessonStatus()
    {
        $lesson = $this->getItem();
        return $this->activity->getLessonStatus($lesson->id);
    }

    /**
     * @return File[]
     */
    public function getFiles()
    {
        $courseId = (int)$this->getState('course.id');
        $lessonId = (int)$this->getState('lesson.id');

        if ($courseId && $lessonId) {
            $db    = $this->getDbo();
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
                ->where(
                    array(
                        'file.courses_id = ' . $courseId,
                        'file.lessons_id = ' . $lessonId
                    )
                )
                ->order('file.ordering ASC, file.title ASC');

            $files = $db->setQuery($query)->loadObjectList(null, '\\Oscampus\\File');

            return $files;
        }

        return array();
    }

    protected function populateState()
    {
        $app = JFactory::getApplication();

        $cid = $app->input->getInt('cid');
        $this->setState('course.id', $cid);

        if ($lid = $app->input->getInt('lid')) {
            $this->setState('lesson.id', $lid);

        } else {
            $index = $app->input->getInt('index');
            $this->setState('lesson.index', $index);
        }

        $uid = $app->input->getInt('uid', JFactory::getUser()->id);
        $this->setState('user.id', $uid);
    }
}

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
     */
    public function getItem()
    {
        if ($this->lesson === null) {
            $this->lesson = OscampusFactory::getContainer()->lesson;
            $pathwayId    = (int)$this->getState('pathway.id');

            if ($lessonId = (int)$this->getState('lesson.id')) {
                $this->lesson->loadById($lessonId, $pathwayId);

            } else {
                $courseId = (int)$this->getState('course.id');
                $index    = (int)$this->getState('lesson.index');

                $this->lesson->loadByIndex($index, $courseId, $pathwayId);
            }
        }

        if ($this->lesson->id && ($uid = (int)$this->getState('user.id'))) {
            $this->activity->setUser($uid);
            $this->activity->visitLesson($this->lesson->id);
        }

        return $this->lesson;
    }

    /**
     * The user activity record for this lesson
     *
     * @return object
     */
    public function getActivityStatus()
    {
        $lesson = $this->getItem();
        return $this->activity->getStatus($lesson->id);
    }

    /**
     * @return File[]
     */
    public function getFiles()
    {
        if ($lessonId = $this->getState('lesson.id')) {
            $db = $this->getDbo();
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
                ->where('flink.lessons_id = ' . (int)$lessonId)
                ->order('flink.ordering ASC, file.title ASC');

            $files = $db->setQuery($query)->loadObjectList(null, '\\Oscampus\\File');

            return $files;
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

<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

use Oscampus\Lesson;

defined('_JEXEC') or die();

class OscampusModelLesson extends OscampusModelSite
{
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
            $pathwayId = (int)$this->getState('pathway.id');

            if ($lessonId = (int)$this->getState('lesson.id')) {
                $this->lesson->loadById($lessonId, $pathwayId);

            } else {
                $courseId = (int)$this->getState('course.id');
                $index = (int)$this->getState('lesson.index');

                $this->lesson->loadByIndex($index, $courseId, $pathwayId);
            }
        }

        return $this->lesson;
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

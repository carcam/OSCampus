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
    public function getLesson()
    {
        if ($this->lesson === null) {
            $pid = (int)$this->getState('pathway.id');
            $cid = (int)$this->getState('course.id');
            $idx = (int)$this->getState('lesson.index');

            $this->lesson = OscampusFactory::getContainer()->lesson;
            $this->lesson->load($pid, $cid, $idx);
        }

        return $this->lesson;
    }

    protected function populateState()
    {
        $app = JFactory::getApplication();

        if ($lid = $app->input->getInt('lid')) {
            $this->setState('lesson.id', $lid);

        } else {
            $pid = $app->input->getInt('pid');
            $this->setState('pathway.id', $pid);

            $cid = $app->input->getInt('cid');
            $this->setState('course.id', $cid);

            $lidx = $app->input->getInt('idx');
            $this->setState('lesson.index', $lidx);
        }

        $uid = $app->input->getInt('uid', JFactory::getUser()->id);
        $this->setState('user.id', $uid);
    }
}

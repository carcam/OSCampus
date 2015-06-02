<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

class OscampusModelLesson extends OscampusModelSite
{
    public function getLesson()
    {
        $lid = (int)$this->getState('lesson.id');
        $db  = $this->getDbo();

        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__oscampus_lessons')
            ->where('id = ' . $lid);

        $lesson = $db->setQuery($query)->loadObject();

        return $lesson;
    }

    protected function populateState()
    {
        $app = JFactory::getApplication();

        $lid = $app->input->getInt('lid');
        $this->setState('lesson.id', $lid);

        $uid = $app->input->getInt('uid', JFactory::getUser()->id);
        $this->setState('user.id', $uid);
    }
}

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

        $data = $db->setQuery($query)->loadObject();

        return new \Oscampus\Lesson($data);
    }

    public function getFiles()
    {
        $lid = (int)$this->getState('lesson.id');
        $db = $this->getDbo();

        $query = $db->getQuery(true)
            ->select('f.*')
            ->from('#__oscampus_files f')
            ->innerJoin('#__oscampus_files_lessons fl ON fl.files_id = f.id')
            ->where('fl.lessons_id = ' . $lid);

        $files = $db->setQuery($query)->loadObjectList();

        return $files;
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

<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

use Oscampus\Factory;

defined('_JEXEC') or die();

class OscampusModelLesson extends OscampusModelSite
{
    /**
     * @var object
     */
    protected $lesson = null;

    /**
     * @var array
     */
    protected $files = null;

    public function getLesson()
    {
        if ($this->lesson === null) {
            $cid = (int)$this->getState('course.id');
            $idx = (int)$this->getState('lesson.index');

            $db = $this->getDbo();

            $query = $db->getQuery(true)
                ->select('l.*')
                ->from('#__oscampus_lessons l')
                ->innerJoin('#__oscampus_modules m ON m.id = l.modules_id')
                ->where('m.courses_id = ' . $cid)
                ->order('m.ordering, l.ordering');

            $data = $db->setQuery($query, $idx, 2)->loadObjectList();

            if ($this->lesson = array_shift($data)) {
                $this->lesson->index = $idx;
                $this->lesson->next = $data ? $idx+1 : null;

                if ($this->lesson->type != 'text') {
                    $this->lesson->content = json_decode($this->lesson->content);
                }
            }
        }

        return $this->lesson;
    }

    public function getFiles()
    {
        if ($this->files === null && $lesson = $this->getLesson()) {
            $db    = $this->getDbo();
            $query = $db->getQuery(true)
                ->select('f.*')
                ->from('#__oscampus_files f')
                ->innerJoin('#__oscampus_files_lessons fl ON fl.files_id = f.id')
                ->where('fl.lessons_id = ' . $lesson->id);

            $this->files = $db->setQuery($query)->loadObjectList();
        }

        return $this->files;
    }

    protected function populateState()
    {
        $app = JFactory::getApplication();

        $cid = $app->input->getInt('cid');
        $this->setState('course.id', $cid);

        $lidx = $app->input->getInt('idx');
        $this->setState('lesson.index', $lidx);

        $uid = $app->input->getInt('uid', JFactory::getUser()->id);
        $this->setState('user.id', $uid);
    }
}

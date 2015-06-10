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
    public function getLesson()
    {
        $cid = (int)$this->getState('course.id');
        $idx = (int)$this->getState('lesson.index');

        $db = $this->getDbo();

        $query = $db->getQuery(true)
            ->select('l.*')
            ->from('#__oscampus_lessons l')
            ->innerJoin('#__oscampus_modules m ON m.id = l.modules_id')
            ->where('m.courses_id = ' . $cid)
            ->order('m.ordering, l.ordering');

        $offset = max(0, $idx - 1);
        $limit  = $idx ? 3 : 2;
        $data   = $db->setQuery($query, $offset, $limit)->loadObjectList();

        foreach ($data as $lesson) {
            if ($lesson->type != 'text') {
                $lesson->content = json_decode($lesson->content);
            }
        }

        if (count($data) === 3) {
            list($previous, $lesson, $next) = $data;
        } elseif ($idx == 0) {
            $previous = null;
            list($lesson, $next) = $data;
        } else {
            list($previous, $lesson) = $data;
            $next = null;
        }

        $lesson->index    = $idx;
        $lesson->previous = $previous;
        $lesson->next     = $next;

        return $lesson;
    }

    public function getFiles()
    {
        $lid = (int)$this->getState('lesson.id');
        $db  = $this->getDbo();

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

        $cid = $app->input->getInt('cid');
        $this->setState('course.id', $cid);

        $lidx = $app->input->getInt('idx');
        $this->setState('lesson.index', $lidx);

        $uid = $app->input->getInt('uid', JFactory::getUser()->id);
        $this->setState('user.id', $uid);
    }
}

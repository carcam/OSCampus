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
            $pid = (int)$this->getState('pathway.id');
            $cid = (int)$this->getState('course.id');
            $idx = (int)$this->getState('lesson.index');

            $db = $this->getDbo();

            $query = $db->getQuery(true)
                ->select('l.*, m.courses_id, m.title module_title, c.title course_title, cp.pathways_id, p.title pathway_title')
                ->from('#__oscampus_lessons l')
                ->innerJoin('#__oscampus_modules m ON m.id = l.modules_id')
                ->innerJoin('#__oscampus_courses c ON c.id = m.courses_id')
                ->innerJoin('#__oscampus_courses_pathways cp ON cp.courses_id = c.id')
                ->innerJoin('#__oscampus_pathways p ON p.id = cp.pathways_id')
                ->where(
                    array(
                        'c.id = ' . $cid,
                        'p.id = ' . $pid
                    )
                )
                ->order('m.ordering, l.ordering');

            $offset = max(0, $idx - 1);
            $limit  = $idx ? 3 : 2;
            $data   = $db->setQuery($query, $offset, $limit)->loadObjectList();

            foreach ($data as $lesson) {
                if ($lesson->type != 'text') {
                    $lesson->content = json_decode($lesson->content);
                }
            }

            $previous = null;
            $next     = null;
            if (count($data) == 3) {
                list($previous, $this->lesson, $next) = $data;
                $previous->index = $idx - 1;
                $next->index     = $idx + 1;

            } elseif ($idx == 0) {
                list($this->lesson, $next) = $data;
                $next->index = $idx + 1;

            } else {
                list($previous, $this->lesson) = $data;
                $previous->index = $idx - 1;
            }

            $this->lesson->index    = $idx;
            $this->lesson->previous = $previous;
            $this->lesson->next     = $next;
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

        $pid = $app->input->getInt('pid');
        $this->setState('pathway.id', $pid);

        $cid = $app->input->getInt('cid');
        $this->setState('course.id', $cid);

        $lidx = $app->input->getInt('idx');
        $this->setState('lesson.index', $lidx);

        $uid = $app->input->getInt('uid', JFactory::getUser()->id);
        $this->setState('user.id', $uid);
    }
}

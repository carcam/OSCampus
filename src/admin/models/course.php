<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();


class OscampusModelCourse extends OscampusModelAdmin
{
    public function getItem($pk = null)
    {
        $item = parent::getItem($pk);

        if (!$item->id) {
            $item->released  = date('Y-m-d');
            $item->access    = 1;
            $item->published = 1;
            $item->image     = \Oscampus\Course::DEFAULT_IMAGE;
            $item->pathways  = array();

        } else {
            $db    = $this->getDbo();
            $query = $db->getQuery(true)
                ->select('pathways_id')
                ->from('#__oscampus_courses_pathways')
                ->where('courses_id = ' . $item->id);

            $item->pathways = $db->setQuery($query)->loadColumn();

            $query = $db->getQuery(true)
                ->select('tags_id')
                ->from('#__oscampus_courses_tags')
                ->where('courses_id = ' . $item->id);

            $item->tags = $db->setQuery($query)->loadColumn();
        }

        return $item;
    }

    public function getLessons($courseId = null)
    {
        if ($courseId = (int)($courseId ?: $this->getState($this->getName() . '.id'))) {
            $db = $this->getDbo();

            $query = $db->getQuery(true)
                ->select(
                    array(
                        'lesson.*',
                        'module.title AS module_title',
                        'module.alias AS module_alias',
                        'module.published AS module_published',
                        'module.ordering AS module_ordering'
                    )
                )
                ->from('#__oscampus_lessons AS lesson')
                ->innerJoin('#__oscampus_modules AS module ON module.id = lesson.modules_id')
                ->where('module.courses_id = 71')
                ->order('module.ordering ASC, lesson.ordering ASC');

            $lessons = $db->setQuery($query)->loadObjectList();
            if ($error = $db->getErrorMsg()) {
                $this->setError($error);
                return false;
            }

            $modules = array();
            foreach ($lessons as $lesson) {
                if (!isset($modules[$lesson->modules_id])) {
                    $modules[$lesson->modules_id] = (object)array(
                        'title'     => $lesson->module_title,
                        'alias'     => $lesson->module_alias,
                        'published' => $lesson->module_published,
                        'ordering'  => $lesson->module_ordering,
                        'lessons'   => array()
                    );
                }
                $modules[$lesson->modules_id]->lessons[$lesson->id] = $lesson;
            }

            return $modules;
        }

        return array();
    }

    public function save($data)
    {
        if (parent::save($data)) {
            // Handle additional update tasks
            $courseId = (int)$this->getState($this->getName() . '.id');
            $pathways = empty($data['pathways']) ? array() : $data['pathways'];
            $tags     = empty($data['tags']) ? array() : $data['tags'];

            return $this->updateJunctionTable(
                '#__oscampus_courses_pathways.courses_id',
                $courseId,
                'pathways_id',
                $pathways
            )
            && $this->updateJunctionTable(
                '#__oscampus_courses_tags.courses_id',
                $courseId,
                'tags_id',
                $tags
            );
        }

        return false;
    }
}

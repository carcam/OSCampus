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

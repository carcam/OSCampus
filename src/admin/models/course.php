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
    /**
     * @param int $pk
     *
     * @return object|false
     */
    public function getItem($pk = null)
    {
        $item = parent::getItem($pk);

        if (!$item->id) {
            $item->setProperties(
                array(
                    'released'  => date('Y-m-d'),
                    'access'    => 1,
                    'published' => 1,
                    'image'     => \Oscampus\Course::DEFAULT_IMAGE,
                    'pathways'  => array(),
                    'tags'      => array()
                )
            );

        } else {
            $item->pathways = $this->getPathways($item->id);
            $item->tags     = $this->getTags($item->id);
        }

        return $item;
    }

    /**
     * @param null $courseId
     *
     * @return array|mixed
     * @throws Exception
     */
    public function getPathways($courseId = null)
    {
        if ($courseId = (int)($courseId ?: $this->getState($this->getName() . '.id'))) {
            $db = $this->getDbo();

            $query = $db->getQuery(true)
                ->select('pathways_id')
                ->from('#__oscampus_courses_pathways')
                ->where('courses_id = ' . $courseId);

            $pathways = $db->setQuery($query)->loadColumn();
            if ($error = $db->getErrorMsg()) {
                $this->setError($error);
            } else {
                return $pathways;
            }
        }

        return array();
    }

    public function getTags($courseId = null)
    {
        if ($courseId = (int)($courseId ?: $this->getState($this->getName() . '.id'))) {
            $db    = $this->getDbo();
            $query = $db->getQuery(true)
                ->select('tags_id')
                ->from('#__oscampus_courses_tags')
                ->where('courses_id = ' . $courseId);

            $tags = $db->setQuery($query)->loadColumn();
            if ($error = $db->getErrorMsg()) {
                $this->setError($error);
            } else {
                return $tags;
            }
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

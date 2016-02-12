<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

class OscampusModelPathway extends OscampusModelSiteList
{
    protected function getListQuery()
    {
        $viewLevels = JFactory::getUser()->getAuthorisedViewLevels();

        $query = parent::getListQuery()
            ->select(
                array(
                    'cp.*',
                    'pathway.title AS pathway',
                    'user.name AS teacher',
                    'course.*'
                )
            )
            ->from('#__oscampus_pathways AS pathway')
            ->innerJoin('#__oscampus_courses_pathways AS cp ON cp.pathways_id = pathway.id')
            ->innerJoin('#__oscampus_courses AS course ON course.id = cp.courses_id')
            ->leftJoin('#__oscampus_teachers AS teacher ON teacher.id = course.teachers_id')
            ->leftJoin('#__users AS user ON user.id = teacher.users_id')
            ->where(
                array(
                    'pathway.id = ' . $this->getState('pathway.id'),
                    'pathway.published = 1',
                    'course.published = 1',
                    'course.access IN (' . join(',', $viewLevels) . ')',
                    'course.released <= NOW()'
                )
            );

        $order = $this->getState('list.order', 'cp.ordering');
        $direction = $this->getState('list.direction', 'ASC');
            $query->order($order . ' ' . $direction . ', course.title ' . $direction);

        return $query;
    }

    public function getItems()
    {
        $items = parent::getItems();

        $tbd = JText::_('COM_OSCAMPUS_TEACHER_UNKNOWN');

        $courses = array();
        foreach ($items as $idx => $item) {
            $courses[$item->id] = $idx;

            $item->tags = array();
            if (!$item->teacher) {
                $item->teacher = $tbd;
            }
        }

        // Format tags
        // @TODO: This is a pretty brain-dead way to handle tags
        $db       = $this->getDbo();
        $tagQuery = $db->getQuery(true)
            ->select('ct.*, tag.title')
            ->from('#__oscampus_courses_tags AS ct')
            ->innerJoin('#__oscampus_tags AS tag ON tag.id = ct.tags_id')
            ->innerJoin('#__oscampus_courses AS course ON course.id = ct.courses_id')
            ->where('course.id IN (' . join(',', array_keys($courses)) . ')');
        $tags     = $db->setQuery($tagQuery)->loadObjectList();
        foreach ($tags as $tag) {
            if (isset($courses[$tag->courses_id])) {
                $idx                 = $courses[$tag->courses_id];
                $items[$idx]->tags[] = $tag->title;
            }
        }
        array_walk($items, function ($item) {
            $item->tags = join(', ', $item->tags);
        });

        return $items;
    }

    /**
     * Get the current pathway information
     *
     * @return object
     */
    public function getPathway()
    {
        $pathway = $this->getState('pathway');
        if ($pathway === null) {
            if ($pid = (int)$this->getState('pathway.id')) {
                $db      = $this->getDbo();
                $pathway = $db->setQuery('Select * From #__oscampus_pathways Where id = ' . $pid)->loadObject();
                $this->setState('pathway', $pathway);
            }
        }
        return $pathway;
    }

    protected function populateState($ordering = null, $direction = null)
    {
        $app = JFactory::getApplication();

        $pathwayId = $app->input->getInt('pid');
        $this->setState('pathway.id', $pathwayId);
    }
}

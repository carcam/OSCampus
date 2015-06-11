<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

class OscampusModelPathway extends OscampusModelList
{
    protected function getListQuery()
    {
        $viewLevels = JFactory::getUser()->getAuthorisedViewLevels();

        $query = parent::getListQuery()
            ->select('cp.*, p.title pathway, u.name teacher, c.*')
            ->from('#__oscampus_pathways p')
            ->innerJoin('#__oscampus_courses_pathways cp ON cp.pathways_id = p.id')
            ->innerJoin('#__oscampus_courses c ON c.id = cp.courses_id')
            ->leftJoin('#__oscampus_teachers i ON i.id = c.teachers_id')
            ->leftJoin('#__users u ON u.id = i.users_id')
            ->where(
                array(
                    'p.id = ' . $this->getState('pathway.id'),
                    'p.published = 1',
                    'p.users_id IS NULL',
                    'c.access IN (' . join(',', $viewLevels) . ')'
                )
            )
            ->order('cp.ordering asc, c.title asc');

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
            ->select('ct.*, t.title')
            ->from('#__oscampus_courses_tags ct')
            ->innerJoin('#__oscampus_tags t ON t.id = ct.tags_id')
            ->innerJoin('#__oscampus_courses c ON c.id = ct.courses_id')
            ->where('c.id IN (' . join(',', array_keys($courses)) . ')');
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

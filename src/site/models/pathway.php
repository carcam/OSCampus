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
        $query = parent::getListQuery()
            ->select('cp.*, p.title pathway, u.name instructor, c.*')
            ->from('#__oscampus_pathways p')
            ->innerJoin('#__oscampus_courses_pathways cp ON cp.pathways_id = p.id')
            ->innerJoin('#__oscampus_courses c ON c.id = cp.courses_id')
            ->leftJoin('#__oscampus_instructors i ON i.id = c.instructors_id')
            ->leftJoin('#__users u ON u.id = i.users_id')
            ->where(
                array(
                    'p.id = ' . $this->getState('pathway.id'),
                    'p.published = 1',
                    'p.users_id IS NULL'
                )
            )
            ->order('cp.ordering asc, c.title asc');

        return $query;
    }

    public function getItems()
    {
        $items = parent::getItems();

        $tbd = JText::_('COM_OSCAMPUS_INSTRUCTOR_UNKNOWN');
        foreach ($items as $item) {
            if (!$item->instructor) {
                $item->instructor = $tbd;
            }
        }
        return $items;
    }

    protected function populateState($ordering = null, $direction = null)
    {
        $app = JFactory::getApplication();

        $pathwayId = $app->input->getInt('pid');
        $this->setState('pathway.id', $pathwayId);
    }
}

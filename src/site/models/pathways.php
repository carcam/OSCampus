<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

class OscampusModelPathways extends OscampusModelList
{
    protected function getListQuery()
    {
        $query = parent::getListQuery()
            ->select('*')
            ->from('#__oscampus_pathways AS pathway')
            ->where(
                array(
                    'pathway.published = 1',
                    $this->getWhereAccess('pathway.access'),
                    'pathway.users_id = 0'
                )
            );

        $ordering  = $this->getState('list.ordering');
        $direction = $this->getState('list.direction');
        $query->order($ordering . ' ' . $direction);
        if ($ordering != 'pathway.title') {
            $query->order('pathway.title ' . $direction);
        }

        return $query;
    }

    protected function populateState($ordering = 'pathway.ordering', $direction = 'ASC')
    {
        parent::populateState($ordering, $direction);

        $this->setState('list.start', 0);
        $this->setState('list.limit', 0);
    }
}

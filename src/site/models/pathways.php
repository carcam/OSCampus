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
        $user = JFactory::getUser();
        $levels  = join(',', $user->getAuthorisedViewLevels());

        $query = parent::getListQuery()
            ->select('*')
            ->from('#__oscampus_pathways')
            ->where(
                array(
                    'published = 1',
                    'access IN (' . $levels . ')'
                )
            )
            ->order('ordering asc');

        return $query;
    }
}

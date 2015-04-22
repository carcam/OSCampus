<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

class OscampusModelPathways extends OscampusModelList
{
    protected function getListQuery()
    {
        $query = parent::getListQuery()
            ->select('*')
            ->from('#__oscampus_pathways')
            ->where('published = 1')
            ->order('ordering asc');

        return $query;
    }
}

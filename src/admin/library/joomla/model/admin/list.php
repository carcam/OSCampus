<?php
/**
 * @package   com_oscampus
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

jimport('joomla.application.component.modellist');

abstract class OscampusModelAdminList extends OscampusModelList
{
    public function getTable($type = '', $prefix = 'OscampusTable', $config = array())
    {
        $type = $type ?: $this->name;

        return OscampusTable::getInstance($type, $prefix, $config);
    }
}

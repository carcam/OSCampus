<?php
/**
 * @package   com_oscampus
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2015 Open Source Training, LLC. All rights reserved
 * @license
 */

use Joomla\String;

defined('_JEXEC') or die();

jimport('joomla.application.component.modellist');

abstract class OscampusModelList extends JModelList
{
    public function getTable($type = '', $prefix = 'OscampusTable', $config = array())
    {
        if (empty($type)) {
            $inflector = String\Inflector::getInstance(true);
            $type = $inflector->toPlural($this->name);
        }

        return OscampusTable::getInstance($type, $prefix, $config);
    }
}

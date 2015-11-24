<?php
/**
 * @package   com_oscampus
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2015 Open Source Training, LLC. All rights reserved
 * @license
 */

use Oscampus\String;

defined('_JEXEC') or die();

jimport('joomla.application.component.modeladmin');

abstract class OscampusModelAdmin extends JModelAdmin
{
    public function getTable($type = '', $prefix = 'OscampusTable', $config = array())
    {
        if (empty($type)) {
            $inflector = String\Inflector::getInstance();
            $type = $inflector->toPlural($this->name);
        }
        return OscampusTable::getInstance($type, $prefix, $config);
    }

    public function getForm($data = array(), $loadData = true)
    {
        $form = $this->loadForm('com_oscampus.' . $this->name, $this->name, array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }

        return $form;
    }

    protected function loadFormData()
    {
        $data = OscampusFactory::getApplication()->getUserState("com_oscampus.edit.{$this->name}.data", array());

        if (empty($data)) {
            $data = $this->getItem();
        }

        return $data;
    }
}

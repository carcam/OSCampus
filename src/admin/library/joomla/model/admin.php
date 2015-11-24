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

    public function save($data)
    {
        if (parent::save($data)) {
            // Handle related table updates
            if ($id = $this->getState($this->getName() . '.id', 0)) {
                $db = $this->getDbo();
                $db->setQuery('DELETE FROM #__oscampus_courses_pathways WHERE courses_id = ' . $id)->execute();
                if ($error = $db->getErrorMsg()) {
                    $this->setError($error);
                    return false;
                }

                $pathways = array_map(
                    function ($row) use ($id) {
                        return sprintf('%s, %s', $id, $row);
                    },
                    (array)$data['pathways']
                );

                $query = $db->getQuery(true)
                    ->insert('#__oscampus_courses_pathways')
                    ->columns('courses_id, pathways_id')
                    ->values($pathways);
                $db->setQuery($query)->execute();
                if ($error = $db->getErrorMsg()) {
                    $this->setError($error);
                    return false;
                }

                return true;
            }
        }

        return false;
    }
}

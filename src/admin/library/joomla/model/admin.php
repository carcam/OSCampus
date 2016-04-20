<?php
/**
 * @package   com_oscampus
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

use Joomla\Registry\Registry as Registry;

defined('_JEXEC') or die();

jimport('joomla.application.component.modeladmin');

abstract class OscampusModelAdmin extends JModelAdmin
{
    public function getItem($pk = null)
    {
        if ($item = parent::getItem($pk)) {
            if (!empty($item->metadata)) {
                $metadata = new Registry($item->metadata);
                $item->metadata = $metadata->toArray();
            }
        }
        return $item;
    }

    public function getTable($type = '', $prefix = 'OscampusTable', $config = array())
    {
        if (empty($type)) {
            $inflector = Oscampus\String\Inflector::getInstance();
            $type      = $inflector->toPlural($this->name);
        }
        return OscampusTable::getInstance($type, $prefix, $config);
    }

    public function getForm($data = array(), $loadData = true)
    {
        $form = $this->loadForm(
            'com_oscampus.' . $this->name,
            $this->name,
            array('control' => 'jform', 'load_data' => $loadData)
        );
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

    /**
     * Utility method for updating standard junction tables.
     *
     * @param string $baseField     Sting in the form table_name.field_name
     * @param int    $baseId        The id of the base ID for the junction
     * @param string $junctionField The field name that is the FK in the target table
     * @param int[]  $junctionIds   The ids for the target table
     *
     * @return bool
     */
    protected function updateJunctionTable($baseField, $baseId, $junctionField, array $junctionIds)
    {
        if ($baseId > 0) {
            $atoms     = explode('.', $baseField);
            $baseField = array_pop($atoms);
            $table     = array_pop($atoms);

            if (empty($table)) {
                $this->setError(JText::sprintf('COM_OSCAMPUS_ERROR_JUNCTION_UPDATE', __CLASS__, __METHOD__));
                return false;
            }

            $db = $this->getDbo();

            $db->setQuery(
                sprintf(
                    'DELETE FROM %s WHERE courses_id = %s',
                    $db->quoteName($table),
                    $baseId
                )
            )
                ->execute();
            if ($error = $db->getErrorMsg()) {
                $this->setError($error);
                return false;
            }

            if ($ids = array_unique(array_filter($junctionIds))) {
                $inserts = array_map(
                    function ($row) use ($baseId) {
                        return sprintf('%s, %s', $baseId, $row);
                    },
                    $ids
                );

                $query = $db->getQuery(true)
                    ->insert($table)
                    ->columns(array($baseField, $junctionField))
                    ->values($inserts);

                $db->setQuery($query)->execute();
                if ($error = $db->getErrorMsg()) {
                    $this->setError($error);
                    return false;
                }
            }
        }
        return true;
    }
}

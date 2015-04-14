<?php
/**
 * @package   com_oscampus
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

abstract class OscampusTable extends JTable
{
    /**
     * @param string $type
     * @param string $prefix
     * @param array  $config
     *
     * @return OscampusTable
     */
    public static function getInstance($type, $prefix = 'OscampusTable', $config = array())
    {
        JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_oscampus/tables');
        return parent::getInstance($type, $prefix, $config);
    }

    /**
     * Automatically set create/modified dates
     *
     * @param boolean $updateNulls [optional]
     *
     * @return boolean
     */
    public function store($updateNulls = false)
    {
        $date = JFactory::getDate()->toSql();
        $user = JFactory::getUser();

        if (empty($this->id) && property_exists($this, 'created')) {
            if ($this->created instanceof DateTime) {
                $this->created = $this->created->format('Y-m-d H:i:s');
            } elseif (!is_string($this->created) || empty($this->created)) {
                $this->created = $date;
            }
        }

        if (empty($this->id) && !empty($user->id)
            && property_exists($this, 'created_by')
            && property_exists($this, 'created_by_alias')
        ) {
            $this->created_by       = $this->created_by ? : $user->id;
            $this->created_by_alias = $this->created_by_alias ? : $user->name;
        }

        if (property_exists($this, 'modified')) {
            $this->modified = $date;
            if (!empty($user->id) && property_exists($this, 'modified_by')) {
                $this->modified_by = $user->id;
            }
        }

        return parent::store($updateNulls);
    }
}

<?php
/**
 * @package   com_oscampus
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

use Joomla\Registry\Registry as Registry;

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
     * @param boolean $updateNulls [optional]
     *
     * @return boolean
     */
    public function store($updateNulls = false)
    {
        $date = JFactory::getDate()->toSql();
        $user = JFactory::getUser();

        // Update standard created fields if they exist
        $key = $this->_tbl_key;
        if ($key && empty($this->$key)) {
            if (property_exists($this, 'created')) {
                if ($this->created instanceof DateTime) {
                    $this->created = $this->created->format('Y-m-d H:i:s');
                } elseif (!is_string($this->created) || empty($this->created)) {
                    $this->created = $date;
                }
            }

            if (!empty($user->id)) {
                if (property_exists($this, 'created_by')) {
                    $this->created_by = $this->created_by ?: $user->id;
                }
                if (property_exists($this, 'created_by_alias')) {
                    $this->created_by_alias = $this->created_by_alias ?: $user->name;
                }
            }
        }

        // Update modified fields if they exist
        if (!$key || !empty($this->$key)) {
            if (property_exists($this, 'modified')) {
                $this->modified = $date;
                if (!empty($user->id) && property_exists($this, 'modified_by')) {
                    $this->modified_by = $user->id;
                }
            }
        }

        return parent::store($updateNulls);
    }

    /**
     * Customised handling for special cases
     *
     * @param array $array
     * @param string $ignore
     *
     * @return bool
     */
    public function bind($array, $ignore = '')
    {
        if (property_exists($this, 'metadata')) {
            if (isset($array['metadata']) && !is_string($array['metadata'])) {
                $registry = new Registry($array['metadata']);
                $array['metadata'] = $registry->toString();
            }
        }

        if (parent::bind($array, $ignore)) {
            // If a table has both alias and title fields, auto-fill an empty alias from the title
            if (property_exists($this, 'alias') && property_exists($this, 'title')) {
                if (empty($this->alias) && !empty($this->title)) {
                    $this->alias = $this->title;
                }
                $this->alias = OscampusApplicationHelper::stringURLSafe($this->alias);
            }
            return true;
        }

        return false;
    }

}

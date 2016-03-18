<?php
/**
 * @package   com_oscampus
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

jimport('joomla.application.component.modellist');

abstract class OscampusModelList extends JModelList
{
    public function getTable($type = '', $prefix = 'OscampusTable', $config = array())
    {
        $type = $type ?: $this->name;

        return OscampusTable::getInstance($type, $prefix, $config);
    }

    /**
     * Provide a generic text search item for a where clause. Takes a variable number
     * of arguments
     *
     * @param JDatabaseQuery $query
     * @param string         $idField
     * @param string         $fieldName,...
     */
    protected function whereTextSearch(JDatabaseQuery $query, $idField, $fieldName)
    {
        $fields  = func_get_args();
        $query   = array_shift($fields);
        $idField = array_shift($fields);

        if ($fields && $search = $this->getState('filter.search')) {
            $db = $this->getDbo();

            if ($idField && stripos(trim($search), 'id:') === 0) {
                $id = (int)substr($search, stripos($search, 'id:') + 3);
                $query->where($idField . ' = ' . $id);

            } else {
                $search = $db->q('%' . $search . '%');
                $ors    = array();
                foreach ($fields as $field) {
                    $ors[] = $db->qn($field) . ' like ' . $search;
                }
                $query->where('(' . join(' OR ', $ors) . ')');
            }
        }
    }

    /**
     * Construct a SQL where item for an access level field
     *
     * @param string $field
     * @param JUser  $user
     *
     * @return string
     */
    protected function getWhereAccess($field, JUser $user = null)
    {
        $user = $user ?: OscampusFactory::getUser();

        $accessLevels = array_unique($user->getAuthorisedViewLevels());

        return sprintf($field . ' IN (%s)', join(', ', $accessLevels));
    }
}

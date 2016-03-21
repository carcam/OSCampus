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
    /**
     * @var string[]
     */
    protected $accessList = array();

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
     * Provide a generic access search for selected field
     *
     * @param string         $field
     * @param JUser          $user
     *
     * @return string
     */
    protected function whereAccess($field, JUser $user = null)
    {
        $user   = $user ?: OscampusFactory::getUser();
        $userId = $user->id;

        if (!isset($this->accessList[$userId])) {
            $this->accessList[$userId] = join(', ', array_unique($user->getAuthorisedViewLevels()));
        }

        if ($this->accessList[$userId]) {
            return sprintf($field . ' IN (%s)', $this->accessList[$userId]);
        }

        return 'TRUE';
    }
}

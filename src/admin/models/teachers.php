<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();


class OscampusModelTeachers extends OscampusModelList
{
    public function __construct($config = array())
    {
        $config['filter_fields'] = array(
            'id', 'teacher.id',
            'name', 'user.name',
            'username', 'user.username',
            'email', 'user.email'
        );

        parent::__construct($config);
    }

    protected function getListQuery()
    {
        $db = $this->getDbo();

        $query = $db->getQuery(true)
            ->select(
                array(
                    'teacher.*',
                    'user.name',
                    'user.username',
                    'user.email',
                    'editor_user.name editor'
                )
            )
            ->from('#__oscampus_teachers teacher')
            ->leftJoin('#__users user ON teacher.users_id = user.id')
            ->leftJoin('#__users editor_user ON editor_user.id = teacher.checked_out');

        $this->whereTextSearch($query, 'user.id', 'user.name', 'user.username', 'user.email');

        $primary = $this->getState('list.ordering', 'user.name');
        $direction = $this->getState('list.direction', 'ASC');
        $query->order($primary . ' ' . $direction);

        return $query;
    }

    protected function populateState($ordering = 'user.name', $direction = 'ASC')
    {
        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        parent::populateState($ordering, $direction);
    }
}

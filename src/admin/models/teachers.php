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

        if ($search = $this->getState('filter.search')) {
            $search = $db->q('%' . $search . '%');
            $ors    = array(
                'user.name like ' . $search,
                'teacher.id like ' . $search
            );
            $query->where('(' . join(' OR ', $ors) . ')');
        }

        $listOrder = $this->getState('list.ordering', 'teacher.id');
        $listDir   = $this->getState('list.direction', 'ASC');
        $query->order($listOrder . ' ' . $listDir);

        return $query;
    }

    protected function populateState($ordering = null, $direction = null)
    {
        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        parent::populateState('user.name', 'ASC');
    }
}

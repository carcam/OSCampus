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
            'name', 'user.name'
        );

        parent::__construct($config);
    }

    protected function getListQuery()
    {
        $db = $this->getDbo();

        $query = $db->getQuery(true);
        $query->select(
            array(
                'teacher.*',
                'user.name',
                'teacher.checked_out as editor'
            )
        );
        $query->from('#__oscampus_teachers teacher');
        $query->leftJoin('#__users user ON teacher.users_id = user.id');

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

        parent::populateState('teacher.id', 'ASC');
    }
}

<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();


class OscampusModelTeachers extends OscampusModelAdminList
{
    public function __construct($config = array())
    {
        $config['filter_fields'] = array(
            'user.name',
            'user.username',
            'user.email',
            'teacher.id'
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
            $fields = array(
                'user.name',
                'user.username',
                'user.email'
            );
            $query->where($this->whereTextSearch($search, $fields, 'user.id'));
        }


        $primary   = $this->getState('list.ordering', 'user.name');
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

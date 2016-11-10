<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();


class OscampusModelPathways extends OscampusModelAdminList
{
    public function __construct($config = array())
    {
        $config['filter_fields'] = array(
            'published',
            'owner',
            'access',
            'pathway.ordering',
            'pathway.published',
            'pathway.title',
            'owner_user.name',
            'viewlevel.title',
            'pathway.id'
        );

        parent::__construct($config);
    }

    protected function getListQuery()
    {
        $db = $this->getDbo();

        $query = $db->getQuery(true);
        $query->select(
            array(
                'pathway.*',
                'owner_user.name AS owner_name',
                'owner_user.username AS owner_username',
                'viewlevel.title AS access_level',
                'editor_user.name AS editor'
            )
        )
            ->from('#__oscampus_pathways AS pathway')
            ->leftJoin('#__users AS owner_user ON owner_user.id = pathway.users_id')
            ->leftJoin('#__viewlevels AS viewlevel ON pathway.access = viewlevel.id')
            ->leftJoin('#__users AS editor_user ON editor_user.id = pathway.checked_out');

        if ($search = $this->getState('filter.search')) {
            $fields = array('pathway.title', 'pathway.alias');
            $query->where($this->whereTextSearch($search, $fields, 'pathway.id'));
        }

        $published = $this->getState('filter.published');
        if ($published != '') {
            $query->where('pathway.published = ' . (int)$published);
        }

        $owner = $this->getState('filter.owner');
        if ($owner != '') {
            $query->where('pathway.users_id = ' . (int)$owner);
        }

        if ($access = (int)$this->getState('filter.access')) {
            $query->where('pathway.access = ' . $access);
        }

        $primary   = $this->getState('list.ordering', 'pathway.title');
        $direction = $this->getState('list.direction', 'ASC');
        $query->order($primary . ' ' . $direction);
        if (!in_array($primary, array('pathway.title', 'pathway.id'))) {
            $query->order('pathway.title ' . $direction);
        }

        return $query;
    }

    protected function populateState($ordering = 'pathway.title', $direction = 'ASC')
    {
        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search', '', 'string');
        $this->setState('filter.search', $search);

        $published = $this->getUserStateFromRequest(
            $this->context . '.filter.published',
            'filter_published',
            '',
            'string'
        );
        $this->setState('filter.published', $published);

        $owner = $this->getUserStateFromRequest($this->context . '.filter.owner', 'filter_owner', '', 'string');
        $this->setState('filter.owner', $owner);

        $access = $this->getUserStateFromRequest($this->context . '.filter.access', 'filter_access', 0, 'int');
        $this->setState('filter.access', $access);

        parent::populateState($ordering, $direction);
    }
}

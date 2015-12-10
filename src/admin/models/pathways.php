<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();


class OscampusModelPathways extends OscampusModelList
{
    public function __construct($config = array())
    {
        $config['filter_fields'] = array(
            'id',           'pathway.id',
            'title',        'pathway.title',
            'published',    'pathway.published',
            'ordering',     'pathway.ordering',
            'access_level', 'viewlevel.title',
            'owner_name',   'owner.name'
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
                'viewlevel.title as access_level',
                'editor_user.name editor'
            )
        )
            ->from('#__oscampus_pathways pathway')
            ->leftJoin('#__users owner ON owner.id = pathway.users_id')
            ->leftJoin('#__viewlevels viewlevel ON pathway.access = viewlevel.id')
            ->leftJoin('#__users editor_user ON editor_user.id = pathway.checked_out');

        $this->whereTextSearch($query, 'pathway.id', 'pathway.title', 'pathway.alias');

        $published = $this->getState('filter.published');
        if ($published != '') {
            $query->where('pathway.published = ' . (int)$published);
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

    protected function populateState($ordering = null, $direction = null)
    {
        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search', '', 'string');
        $this->setState('filter.search', $search);

        $published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '', 'string');
        $this->setState('filter.published', $published);
        
        $access = $this->getUserStateFromRequest($this->context . '.filter.access', 'filter_access', 0, 'int');
        $this->setState('filter.access', $access);

        parent::populateState('pathway.title', 'ASC');
    }
}

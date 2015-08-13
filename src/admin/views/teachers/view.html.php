<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

class OscampusViewTeachers extends OscampusViewAdmin
{
    public function display($tpl = null)
    {
        $state = $this->get('State');

        $this->setVariable('list_order', $this->escape($state->get('list.ordering')));
        $this->setVariable('list_dir', $this->escape($state->get('list.direction')));
        $this->setVariable('items', $this->get('Items'));
        $this->setVariable('pagination', $this->get('Pagination')->getListFooter());

        // $this->filterForm    = $this->get('FilterForm');
        // $this->activeFilters = $this->get('ActiveFilters');

        // if (count($errors = $this->get('Errors'))) {
        //     throw new Exception(implode("\n", $errors));
        // }

        // $notices = OscampusHelper::getNotices();
        // OscampusHelper::enqueueMessages($notices);

        parent::display($tpl);
    }

    /**
     * Default admin screen title
     *
     * @param string $sub
     * @param string $icon
     *
     * @return void
     */
    protected function setTitle($sub = null, $icon = 'oscampus')
    {
        parent::setTitle('COM_OSCAMPUS_SUBMENU_TEACHERS', $icon);
    }

    protected function setToolbar()
    {
        OscampusToolbarHelper::addNew('teacher.add');
        OscampusToolbarHelper::editList('teacher.edit');
        OscampusToolbarHelper::deleteList('COM_OSCAMPUS_DELETE_CONFIRM', 'teachers.delete');

        parent::setToolbar();
    }
}

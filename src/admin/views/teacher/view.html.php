<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

class OscampusViewTeacher extends OscampusViewAdmin
{
    protected $item;

    public function display($tpl = null)
    {
        $state = $this->get('State');

        $this->item = $this->get('Item');

        $this->setVariable('form', $this->get('Form'));
        $this->setVariable('item', $this->item);

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
        $isNew = ($this->item->id == 0);
        $title = 'COM_OSCAMPUS_PAGE_VIEW_TEACHER_' . ($isNew ? 'ADD' : 'EDIT');

        parent::setTitle($title, $icon);
    }

    protected function setToolbar()
    {
        $isNew = ($this->item->id == 0);
        OscampusFactory::getApplication()->input->set('hidemainmenu', true);

        OscampusToolbarHelper::apply('teacher.apply');
        OscampusToolbarHelper::save('teacher.save');

        $alt = $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE';
        OscampusToolbarHelper::cancel('teacher.cancel', $alt);

        parent::setToolbar();
    }
}

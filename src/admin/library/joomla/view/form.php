<?php
/**
 * @package   com_oscampus
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();


abstract class OscampusViewForm extends OscampusViewAdmin
{
    protected $item;

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
        $name  = strtoupper($this->getName());
        $title = "COM_OSCAMPUS_PAGE_VIEW_{$name}_" . ($isNew ? 'ADD' : 'EDIT');

        parent::setTitle($title, $icon);
    }

    public function display($tpl = null)
    {
        $state = $this->get('State');

        $this->item = $this->get('Item');

        $this->setVariable('form', $this->get('Form'));
        $this->setVariable('item', $this->item);

        parent::display($tpl);
    }

    protected function setToolbar()
    {
        OscampusFactory::getApplication()->input->set('hidemainmenu', true);

        $controller = $this->getName();

        OscampusToolbarHelper::apply($controller . '.apply');
        OscampusToolbarHelper::save($controller . '.save');

        $isNew = ($this->item->id == 0);
        $alt   = $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE';
        OscampusToolbarHelper::cancel($controller . '.cancel', $alt);

        parent::setToolbar();
    }
}

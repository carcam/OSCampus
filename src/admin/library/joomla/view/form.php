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
    /**
     * Instance of the item with to fill the form
     *
     * @var stdClass
     */
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

    /**
     * Method to display the view
     *
     * @param  string $tpl The name of the template file to parse
     *
     * @return void
     */
    public function display($tpl = null)
    {
        $state = $this->get('State');

        $this->item = $this->get('Item');

        $this->setVariable('form', $this->get('Form'));
        $this->setVariable('item', $this->item);

        parent::display($tpl);
    }

    /**
     * Method to set default buttons to the toolbar
     *
     * @return  void
     */
    protected function setToolbar()
    {
        OscampusFactory::getApplication()->input->set('hidemainmenu', true);

        $controller = $this->getName();

        OscampusToolbarHelper::apply($controller . '.apply');
        OscampusToolbarHelper::save($controller . '.save');
        OscampusToolbarHelper::save2new($controller . '.save2new');
        OscampusToolbarHelper::save2copy($controller . '.save2copy');

        $isNew = ($this->item->id == 0);
        $alt   = $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE';
        OscampusToolbarHelper::cancel($controller . '.cancel', $alt);
    }
}

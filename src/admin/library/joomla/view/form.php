<?php
/**
 * @package   com_oscampus
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2015-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

abstract class OscampusViewForm extends OscampusViewAdmin
{
    /**
     * @var OscampusModelAdmin
     */
    protected $model = null;

    /**
     * @var JForm
     */
    protected $form = null;

    /**
     * @var OscampusTable
     */
    protected $item = null;

    protected function setup()
    {
        parent::setup();

        $this->model = $this->getModel();
        $this->form  = $this->model->getForm();
        $this->item  = $this->model->getItem();

        $this->setVariable(
            array(
                'form' => $this->form,
                'item' => $this->item
            )
        );
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
        $isNew = empty($this->item->id);
        $name  = strtoupper($this->getName());
        $title = "COM_OSCAMPUS_PAGE_VIEW_{$name}_" . ($isNew ? 'ADD' : 'EDIT');

        parent::setTitle($title, $icon);
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
        //OscampusToolbarHelper::save2copy($controller . '.save2copy');

        $isNew = empty($this->item->id);
        $alt   = $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE';
        OscampusToolbarHelper::cancel($controller . '.cancel', $alt);
    }
}

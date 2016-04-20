<?php
/**
 * @package   com_oscampus
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();


abstract class OscampusViewAdmin extends OscampusViewTwig
{
    /**
     * @return void
     */
    protected function setup()
    {
        // For use in subclasses
    }

    /**
     * Render the view
     *
     * @param  string $tpl
     *
     * @return void|Exception
     */
    public function display($tpl = null)
    {
        $this->setup();

        $this->setTitle();
        $this->setToolbar();
        $this->setSubmenu();

        $this->displayHeader();
        parent::display($tpl);
        $this->displayFooter();
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
        $img = JHtml::_('image', "com_oscampus/icon-48-{$icon}.png", null, null, true, true);
        if ($img) {
            $doc = OscampusFactory::getDocument();
            $doc->addStyleDeclaration(".icon-48-{$icon} { background-image: url({$img}); }");
        }

        $title = JText::_('COM_OSCAMPUS');
        if ($sub) {
            $title .= ': ' . JText::_($sub);
        }

        OscampusToolbarHelper::title($title, $icon);
    }

    /**
     * Set the admin screen toolbar buttons
     *
     * @return void
     */
    protected function setToolbar()
    {
        $user = OscampusFactory::getUser();
        if ($user->authorise('core.admin', 'com_oscampus')) {
            $items = JToolBar::getInstance('toolbar')->getItems();

            if (!empty($items)) {
                OscampusToolbarHelper::divider();
            }

            OscampusToolbarHelper::preferences('com_oscampus');
        }
    }

    /**
     * Display a header on admin pages
     *
     * @return void
     */
    protected function displayHeader()
    {
        // To be set in subclasses
    }

    /**
     * Display a standard footer on all admin pages
     *
     * @return void
     */
    protected function displayFooter()
    {
        // To be set in subclassess
    }

    /**
     * Add a new submenu
     *
     * @param string $name The submenu's text
     * @param string $view The submenu's view
     */
    protected function addSubmenuItem($name, $view, $active)
    {
        $link = 'index.php?option=com_oscampus&view=' . $view;

        JHtmlSidebar::addEntry(JText::_($name), $link, $active);
    }

    /**
     * Set the submenu items
     */
    protected function setSubmenu()
    {
        $app = OscampusFactory::getApplication();

        $hide = $app->input->getBool('hidemainmenu', false);
        if (!$hide) {
            $this->addSubmenuItem('COM_OSCAMPUS_SUBMENU_COURSES', 'courses', $this->_name == 'courses');
            $this->addSubmenuItem('COM_OSCAMPUS_SUBMENU_LESSONS', 'lessons', $this->_name == 'lessons');
            $this->addSubmenuItem('COM_OSCAMPUS_SUBMENU_PATHWAYS', 'pathways', $this->_name == 'pathways');
            $this->addSubmenuItem('COM_OSCAMPUS_SUBMENU_TAGS', 'tags', $this->_name == 'tags');
            $this->addSubmenuItem('COM_OSCAMPUS_SUBMENU_TEACHERS', 'teachers', $this->_name == 'teachers');
        }

        $this->setVariable('show_sidebar', !$hide);
    }
}

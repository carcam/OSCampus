<?php
/**
 * @package   com_oscampus
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2015-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();


abstract class OscampusViewAdmin extends OscampusViewTwig
{
    protected $option = null;

    protected function setup()
    {
        parent::setup();

        $this->option = OscampusFactory::getApplication()->input->getCmd('option', 'com_oscampus');

        $this->setTitle();
        $this->setToolbar();
        $this->setSubmenu();
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
            $items = JToolbar::getInstance('toolbar')->getItems();

            if (!empty($items)) {
                OscampusToolbarHelper::divider();
            }

            OscampusToolbarHelper::preferences('com_oscampus');
        }
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

    protected function displayFooter()
    {
        $result = '';

        $path = JPATH_COMPONENT_ADMINISTRATOR .'/views/footer/tmpl/default.php';
        if (is_file($path)) {
            ob_start();
            include_once $path;

            $result = ob_get_contents();
            ob_end_clean();
        }

        return parent::displayFooter() . $result;
    }
}

<?php
/**
 * @package   com_oscampus
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();


abstract class OscampusViewAdmin extends OscampusViewTwig
{
    /**
     * @var JObject
     */
    protected $state = null;

    /**
     * Constructor
     *
     * @param array $config Optional configuration
     */
    public function __construct($config = array())
    {
        parent::__construct($config);

        $this->state = $this->get('State');
    }

    /**
     * Render the view
     *
     * @param  string $tpl
     */
    public function display($tpl = null)
    {
        $this->setTitle();
        $this->setToolBar();
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

        JToolbarHelper::title($title, $icon);
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
                JToolBarHelper::divider();
            }

            JToolBarHelper::preferences('com_oscampus');
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
     * @param string  $text   The submenu's text
     * @param string  $view   The submenu's view
     */
    protected function addSubmenuItem($label, $view)
    {
        if (!isset($this->variables['submenu_items'])) {
            $this->setVariable('submenu_items', array());
        }

        $currentView = OscampusFactory::getApplication()->input->get('view', 'dashboard');
        $link        = 'index.php?option=com_oscampus&view=' . $view;
        $active      = $currentView === $view;

        $item = new stdClass;
        $item->label  = JText::_($label);
        $item->link   = $link;
        $item->active = (bool)$active;
        $item->class  = $active ? 'active' : '';

        $this->variables['submenu_items'][] = $item;
    }

    /**
     * Set the submenu items
     */
    protected function setSubmenu()
    {
        $this->addSubmenuItem('COM_OSCAMPUS_SUBMENU_DASHBOARD', 'dashboard');
        $this->addSubmenuItem('COM_OSCAMPUS_SUBMENU_TEACHERS', 'teachers');
    }
}

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

    public function __construct($config = array())
    {
        parent::__construct($config);

        $this->state = $this->get('State');
    }

    public function display($tpl = null)
    {
        $this->setTitle();

        $this->displayHeader();

        $hide    = OscampusFactory::getApplication()->input->getBool('hidemainmenu', false);
        $sidebar = count(JHtmlSidebar::getEntries()) + count(JHtmlSidebar::getFilters());
        if (!$hide && $sidebar > 0) {
            $start = array(
                '<div id="j-sidebar-container" class="span2">',
                JHtmlSidebar::render(),
                '</div>',
                '<div id="j-main-container" class="span10">'
            );

        } else {
            $start = array('<div id="j-main-container">');
        }

        echo join("\n", $start) . "\n";
        parent::display($tpl);
        echo "\n</div>";

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
     * Render the admin screen toolbar buttons
     *
     * @param bool $addDivider
     *
     * @return void
     */
    protected function setToolBar($addDivider = true)
    {
        $user = OscampusFactory::getUser();
        if ($user->authorise('core.admin', 'com_oscampus')) {
            if ($addDivider) {
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
}

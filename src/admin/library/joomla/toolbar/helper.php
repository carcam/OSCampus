<?php
/**
 * @package   com_oscampus
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

abstract class OscampusToolbarHelper extends JToolbarHelper
{
    /**
     * Add a custom button with standard OSCampus styles
     *
     * @param string $task
     * @param string $icon
     * @param string $iconOver
     * @param string $alt
     * @param bool   $listSelect
     * @param string $iconColor
     *
     * @return void
     */
    public static function custom(
        $task = '',
        $icon = '',
        $iconOver = '',
        $alt = '',
        $listSelect = true,
        $iconColor = '#333'
    ) {
        $img = JHtml::_('image', "com_oscampus/icon-32-{$icon}.png", null, null, true, true);
        if ($img) {
            $doc = OscampusFactory::getDocument();

            $doc->addStyleDeclaration(".icon-{$icon}:before { color: {$iconColor}; }");
        }
        parent::custom($task, $icon, $iconOver, $alt, $listSelect);
    }

    /**
     * Add the batch button
     *
     * @param string $title
     * @param string $layout
     *
     * @return void
     */
    public static function batch($title = '', $layout = 'joomla.toolbar.batch')
    {
        // Instantiate a new JLayoutFile instance and render the batch button
        $layout = new JLayoutFile($layout);

        $title = $title ?: JText::_('JTOOLBAR_BATCH');

        $bar  = JToolbar::getInstance('toolbar');
        $html = $layout->render(array('title' => $title));
        $bar->appendButton('Custom', $html, 'batch');
    }
}

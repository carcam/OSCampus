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

            if (version_compare(JVERSION, '3.0', 'lt')) {
                $doc->addStyleDeclaration(
                    ".icon-32-{$icon} { background-image: url({$img}); background-repeat: no-repeat; }"
                );

            } else {
                $doc->addStyleDeclaration(".icon-{$icon}:before { color: {$iconColor}; }");
            }
        }
        parent::custom($task, $icon, $iconOver, $alt, $listSelect);
    }

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

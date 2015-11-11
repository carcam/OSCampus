<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

JLoader::register('JHtmlGrid', JPATH_LIBRARIES . '/joomla/html/html/grid.php');

abstract class OscGrid extends JHtmlGrid
{
    public static function checkall(
        $name = 'checkall-toggle',
        $tip = 'JGLOBAL_CHECK_ALL',
        $action = 'Joomla.checkAll(this)'
    ) {

        if (is_callable('parent::checkall')) {
            return parent::checkall($name, $tip, $action);
        }

        JHtml::_('behavior.tooltip');

        $attribs = array(
            'type'    => 'checkbox',
            'name'    => $name,
            'value'   => '',
            'class'   => 'hasTooltip',
            'title'   => JText::_($tip),
            'onclick' => $action
        );

        return '<input ' . OscampusUtilitiesArray::toString($attribs) . ' />';
    }
}

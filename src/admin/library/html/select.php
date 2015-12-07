<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

/**
 * Because the JHtml classes do not handle namespacing very well,
 * we must load it here or the JHtmlSelect class will not be available.
 */
$paths = array(
    JPATH_LIBRARIES . '/cms/html',
    JPATH_LIBRARIES . '/joomla/html/html'
);
foreach ($paths as $path) {
    if (is_file($path . '/select.php')) {
        require_once $path . '/select.php';
        break;
    }
}

abstract class OscSelect
{
    protected static function joomla_version()
    {
        $version = explode('.', JVERSION);
        return (string)array_shift($version);
    }

    /**
     * Create a publishing dropdwon selector
     *
     * @param string $name
     * @param mixed  $selected
     * @param string $blankOption
     * @param mixed  $attribs
     * @param string $id
     *
     * @return string
     */
    public static function published($name, $selected, $blankOption = null, $attribs = null, $id = null)
    {
        $options = array(
            JHtml::_('select.option', '0', JText::_('JUNPUBLISHED')),
            JHtml::_('select.option', '1', JText::_('JPUBLISHED'))
        );
        if ($blankOption) {
            array_unshift($options, JHtml::_('select.option', '', JText::_($blankOption)));
        }

        return JHtml::_('select.genericlist', $options, $name, $attribs, 'value', 'text', $selected, $id);
    }
}
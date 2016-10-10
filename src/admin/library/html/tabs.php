<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

/**
 * A set of wrapper methods to smooth the differences between J!2 and J!3 in creating tabbed pages
 *
 * First we search for  the native JHtmlTabs and load it. Because the JHtml classes do not handle
 * namespacing very well, we must do this or the JHtmlTabs class will not be available.
 */
$paths = array(
    JPATH_LIBRARIES . '/cms/html',
    JPATH_LIBRARIES . '/joomla/html/html'
);
foreach ($paths as $path) {
    if (is_file($path . '/tabs.php')) {
        require_once $path . '/tabs.php';
        break;
    }
}

abstract class OscTabs
{
    protected static function joomlaVersion()
    {
        $version = explode('.', JVERSION);
        return (string)array_shift($version);
    }

    public static function startset($group = 'tabs', $params = array())
    {
        if (static::joomlaVersion() == '2') {
            return JHtml::_('tabs.start', $group, $params);
        }

        return JHtml::_('bootstrap.startTabSet', $group, $params);
    }

    public static function add($group, $text, $id)
    {
        if (static::joomlaVersion() == '2') {
            return JHtml::_('tabs.panel', $text, $id) . '<div class="row-fluid">';
        }

        return JHtml::_('bootstrap.addTab', $group, $id, $text);

    }

    public static function end()
    {
        if (static::joomlaVersion() == '2') {
            return '<div class="clr"></div>';
        }

        return JHtml::_('bootstrap.endtab');
    }

    public static function endset()
    {
        if (static::joomlaVersion() == '2') {
            return JHtml::_('tabs.end');
        }

        return JHtml::_('bootstrap.endTabSet');
    }

}

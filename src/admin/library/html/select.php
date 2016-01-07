<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
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
    protected static $cache = array();

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

    /**
     * @param string       $name
     * @param int          $selected
     * @param string|array $addOptions
     * @param array|string $attribs
     * @param string       $id
     *
     * @return string
     */
    public static function pathway($name, $selected, $addOptions = null, $attribs = null, $id = null)
    {
        $options = array_merge(static::createAddOptions($addOptions), JHtml::_('osc.options.pathways'));

        return JHtml::_('select.genericlist', $options, $name, $attribs, 'value', 'text', $selected, $id);
    }

    /**
     * @param string       $name
     * @param int          $selected
     * @param array|string $addOptions
     * @param array|string $attribs
     * @param string       $id
     *
     * @return string
     */
    public static function tag($name, $selected, $addOptions = null, $attribs = null, $id = null)
    {
        $options = array_merge(static::createAddOptions($addOptions), JHtml::_('osc.options.tags'));

        return JHtml::_('select.genericlist', $options, $name, $attribs, 'value', 'text', $selected, $id);

    }

    /**
     * @param string       $name
     * @param string       $selected
     * @param array|string $addOptions
     * @param array|string $attribs
     * @param string       $id
     *
     * @return string
     */
    public static function difficulty($name, $selected, $addOptions = null, $attribs = null, $id = null)
    {
        $options = array_merge(static::createAddOptions($addOptions), JHtml::_('osc.options.difficulties'));

        return JHtml::_('select.genericlist', $options, $name, $attribs, 'value', 'text', $selected, $id);
    }

    /**
     * @param string       $name
     * @param string       $selected
     * @param array|string $addOptions
     * @param array|string $attribs
     * @param string       $id
     *
     * @return string
     */
    public static function access($name, $selected, $addOptions = null, $attribs = null, $id = null)
    {
        $accessGroups = JHtml::_('access.assetgroups');

        $options = array_merge(static::createAddOptions($addOptions), $accessGroups);

        return JHtml::_('select.genericlist', $options, $name, $attribs, 'value', 'text', $selected, $id);
    }

    /**
     * @param string       $name
     * @param string       $selected
     * @param array|string $addOptions
     * @param array|string $attribs
     * @param string       $id
     *
     * @return string
     */
    public static function teacher($name, $selected, $addOptions = null, $attribs = null, $id = null)
    {
        $options = array_merge(static::createAddOptions($addOptions), JHtml::_('osc.options.teachers'));

        return JHtml::_('select.genericlist', $options, $name, $attribs, 'value', 'text', $selected, $id);
    }

    /**
     * @param string       $name
     * @param string       $selected
     * @param array|string $addOptions
     * @param array|string $attribs
     * @param string       $id
     *
     * @return string
     */
    public static function lessontype($name, $selected, $addOptions = null, $attribs = null, $id = null)
    {
        $options = array_merge(static::createAddOptions($addOptions), JHtml::_('osc.options.lessontypes'));

        return JHtml::_('select.genericlist', $options, $name, $attribs, 'value', 'text', $selected, $id);
    }

    /**
     * @param string       $name
     * @param string|int   $selected
     * @param array|string $addOptions
     * @param array|string $attribs
     * @param string       $id
     *
     * @return mixed
     */
    public static function pathwayowner($name, $selected, $addOptions = null, $attribs = null, $id = null)
    {
        if ($users = JHtml::_('osc.options.pathwayowners')) {
            $options = array_merge(static::createAddOptions($addOptions), $users);

        } else {
            $options = array(
                JHtml::_('select.option', null, JText::_('COM_OSCAMPUS_OPTION_NO_PATHWAY_OWNERS'), 'value', 'text',
                    true)
            );
        }

        return JHtml::_('select.genericlist', $options, $name, $attribs, 'value', 'text', $selected, $id);
    }

    /**
     * @param array|string $texts
     *
     * @return array
     */
    protected static function createAddOptions($texts)
    {
        $options = array();
        if ($texts) {
            foreach ((array)$texts as $value => $text) {
                $options[] = JHtml::_('select.option', $value, JText::_($text));
            }
        }

        return $options;
    }
}

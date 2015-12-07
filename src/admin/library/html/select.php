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
        if (!isset(static::$cache['pathways'])) {
            $db = OscampusFactory::getDbo();

            $query = $db->getQuery(true)
                ->select('pathway.id, pathway.title, viewlevel.title viewlevel_title')
                ->from('#__oscampus_pathways AS pathway')
                ->leftJoin('#__viewlevels AS viewlevel ON viewlevel.id = pathway.access')
                ->order('title');

            static::$cache['pathways'] = $db->setQuery($query)->loadObjectList();
            array_walk(static::$cache['pathways'], function (&$row) {
                $row = (object)array(
                    'value' => $row->id,
                    'text'  => sprintf('%s (%s)', $row->title, $row->viewlevel_title)
                );
            });
        }

        $options = array_merge(static::createAddOptions($addOptions), static::$cache['pathways']);

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
        if (!isset(static::$cache['tags'])) {
            $db = OscampusFactory::getDbo();

            $query = $db->getQuery(true)
                ->select(
                    array(
                        'tag.id AS ' . $db->nq('value'),
                        'tag.title AS ' . $db->nq('text')
                    )
                )
                ->from('#__oscampus_tags AS tag')
                ->order('title');

            static::$cache['tags'] = $db->setQuery($query)->loadObjectList();
        }

        $options = array_merge(static::createAddOptions($addOptions), static::$cache['tags']);

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

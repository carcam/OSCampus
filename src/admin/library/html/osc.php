<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

abstract class JHtmlOsc
{
    protected static $jqueryLoaded    = false;
    protected static $utilitiesLoaded = false;

    /**
     * Load jQuery core
     *
     * @param bool $utilities
     * @param bool $noConflict
     * @param bool $debug
     */
    public static function jquery($utilities = true, $noConflict = true, $debug = null)
    {
        $params = OscampusComponentHelper::getParams();

        $load   = $params->get('advanced.jquery', 0);
        $client = JFactory::getApplication()->getName();
        if ($load == $client || $load == 1) {
            // Only load once
            if (!static::$jqueryLoaded) {
                if (version_compare(JVERSION, '3.0', 'lt')) {
                    // pre 3.0 manual loading

                    // If no debugging value is set, use the configuration setting
                    if ($debug === null) {
                        $config = JFactory::getConfig();
                        $debug  = (boolean)$config->get('debug');
                    }

                    JHtml::_('script', 'com_oscampus/jquery.js', false, true, false, false, $debug);

                    // Check if we are loading in noConflict
                    if ($noConflict) {
                        JHtml::_('script', 'com_oscampus/jquery-noconflict.js', false, true, false, false, false);
                    }
                } else {
                    JHtml::_('jquery.framework', $noConflict, $debug);
                }
            }
        }
        static::$jqueryLoaded = true;

        if ($utilities && !static::$utilitiesLoaded) {
            JHtml::_('script', 'com_oscampus/utilities.js', false, true);
            static::$utilitiesLoaded = true;
        }
    }

    /**
     * Make a collection of elements sortable by dragging
     *
     * @param string $selector
     *
     * @return void
     */
    public static function sortable($selector)
    {
        static::jquery();
        JHtml::_('script', 'com_oscampus/jquery-ui.js', false, true);

        $options = json_encode(array('selector' => $selector));
        static::onready("$.Oscampus.sortable({$options})");
    }

    /**
     * Setup tabbed areas
     *
     * @param string       $selector jQuery selector for tab headers
     * @param array|string $options  Associative array or JSON string of tabber options
     *
     * @return void
     */
    public static function tabs($selector, $options = null)
    {
        static::jquery();

        if ($options && is_string($options)) {
            $options = json_decode($options, true);
        }
        if (!is_array($options)) {
            $options = array();
        }
        $options['selector'] = $selector;

        $options = json_encode($options);
        static::onready("$.Oscampus.tabs({$options});");
    }

    /**
     * Setup simple sliders
     *
     * @param string $selector
     * @param bool   $visible
     *
     * @return void
     */
    public static function sliders($selector, $visible = false)
    {
        static::jquery();

        $options = json_encode(
            array(
                'selector' => $selector,
                'visible'  => (bool)$visible
            )
        );

        static::onready("$.Oscampus.sliders({$options});");
    }

    /**
     * Add a script to run when dom ready
     *
     * @param string $js
     *
     * @return void
     */
    public static function onready($js)
    {
        $js = "(function($) { $(document).ready(function () { " . $js . " });})(jQuery);";
        OscampusFactory::getDocument()->addScriptDeclaration($js);
    }

    /**
     * Build link to a course from its ID alone
     *
     * @param int    $cid
     * @param string $text
     * @param mixed  $attribs
     * @param bool   $uriOnly
     *
     * @return string
     */
    public static function courselink($pid, $cid, $text, $attribs = null, $uriOnly = false)
    {
        if ((int)$cid) {
            $app   = JFactory::getApplication();
            $query = OscampusRoute::getInstance()->getQuery('pathway');

            $query['view'] = 'course';
            $query['cid']  = (int)$cid;
            if ($pid) {
                $query['pid'] = $pid;
            }

            $link = 'index.php?' . http_build_query($query);
            if ($uriOnly) {
                return $link;
            }
            return JHtml::_('link', JRoute::_($link), $text, $attribs);
        }
        return '';
    }

    /**
     * Build link to a pathway from its ID alone
     *
     * @param int    $pid
     * @param string $text
     * @param null   $attribs
     * @param bool   $uriOnly
     *
     * @return string
     */
    public static function pathwaylink($pid, $text, $attribs = null, $uriOnly = false)
    {
        if ((int)$pid) {
            $app   = JFactory::getApplication();
            $query = OscampusRoute::getInstance()->getQuery('pathways');

            $query['view'] = 'pathway';
            $query['pid']  = (int)$pid;

            $link = 'index.php?' . http_build_query($query);
            if ($uriOnly) {
                return $link;
            }

            return JHtml::_('link', JRoute::_($link), $text, $attribs);
        }
        return '';
    }

    /**
     * Build link to a lesson on course ID/Index alone
     *
     * @param int    $cid
     * @param int    $index
     * @param string $text
     * @param mixed  $attribs
     * @param bool   $uriOnly
     *
     * @return string
     */
    public static function lessonlink($pid, $cid, $index, $text, $attribs = null, $uriOnly = false)
    {
        if ((int)$cid) {
            $app   = JFactory::getApplication();
            $query = OscampusRoute::getInstance()->getQuery('course');

            $query['view'] = 'lesson';
            $query['cid']  = (int)$cid;
            $query['idx']  = (int)$index;
            $query['pid']  = $pid;

            $link = 'index.php?' . http_build_query($query);
            if ($uriOnly) {
                return $link;
            }

            return JHtml::_('link', JRoute::_($link), $text, $attribs);
        }

        return '';
    }
}

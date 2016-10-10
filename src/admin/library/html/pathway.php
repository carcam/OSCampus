<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

abstract class OscPathway
{
    /**
     * @param object $pathway
     * @param string $text
     * @param mixed  $attribs
     * @param bool   $uriOnly
     *
     * @return string
     */
    public static function link($pathway, $text = null, $attribs = null, $uriOnly = false)
    {
        $query        = OscampusRoute::getInstance()->getQuery('pathway');
        $query['pid'] = $pathway->id;

        $link = 'index.php?' . http_build_query($query);

        if ($uriOnly) {
            return $link;
        }

        return JHtml::_('link', JRoute::_($link), $text ?: $pathway->title, $attribs);
    }
}

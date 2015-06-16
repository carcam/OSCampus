<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

abstract class OscPathway
{
    public static function link($pathway, $text = null, $attribs = null, $uriOnly = false)
    {
        $query        = OscampusRoute::getInstance()->getQuery('pathway');
        $query['pid'] = $pathway->id;

        $link = JRoute::_('index.php?' . http_build_query($query));

        if ($uriOnly) {
            return $link;
        }

        return JHtml::_('link', $link, $text ?: $pathway->title, $attribs);
    }
}

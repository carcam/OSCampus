<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

abstract class OscLink
{
    /**
     * Build link to a course from its ID alone
     *
     * @param int    $pid
     * @param int    $cid
     * @param string $text
     * @param mixed  $attribs
     * @param bool   $uriOnly
     *
     * @return string
     */
    public static function course($pid, $cid, $text, $attribs = null, $uriOnly = false)
    {
        if ((int)$cid) {
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
    public static function pathway($pid, $text, $attribs = null, $uriOnly = false)
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
     * @param int    $pid
     * @param int    $cid
     * @param int    $index
     * @param string $text
     * @param mixed  $attribs
     * @param bool   $uriOnly
     *
     * @return string
     */
    public static function lesson($pid, $cid, $index, $text, $attribs = null, $uriOnly = false)
    {
        if ((int)$cid) {
            $query = OscampusRoute::getInstance()->getQuery('course');

            $query['view']  = 'lesson';
            $query['pid']   = $pid;
            $query['cid']   = (int)$cid;
            $query['index'] = (int)$index;

            $link = 'index.php?' . http_build_query($query);
            if ($uriOnly) {
                return $link;
            }

            return JHtml::_('link', JRoute::_($link), $text, $attribs);
        }

        return '';
    }

    /**
     * Create link to a single certificate
     *
     * @param int          $id
     * @param string       $text
     * @param array|string $attribs
     * @param bool         $uriOnly
     * @param bool         $fullUri
     *
     * @return string
     */
    public static function certificate($id, $text = null, $attribs = null, $uriOnly = false, $fullUri = false)
    {
        $text = $text ?: '<i class="fa fa-download"></i> ' . JText::_('COM_OSCAMPUS_DOWNLOAD_PDF');

        $query = array(
            'option' => 'com_oscampus',
            'view'   => 'certificate',
            'format' => 'pdf',
            'id'     => (int)$id
        );

        $link = JRoute::_('index.php?' . http_build_query($query));

        if ($fullUri) {
            $link = static::absoluteLink($link);
        }

        if ($uriOnly) {
            return $link;
        }

        return JHtml::_('link', $link, $text, $attribs);
    }

    /**
     * Turn a relative url into an absolute url
     *
     * @param string $relativeLink
     *
     * @return string
     */
    protected static function absoluteLink($relativeLink)
    {
        return str_replace('//', '/', OscampusFactory::getURI()->root() . $relativeLink);
    }
}

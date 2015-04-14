<?php
/**
 * @package   com_oscampus
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

abstract class JHtmlDatetime
{
    /**
     * @param mixed  $dateTime
     * @param string $blank
     * @param string $format
     *
     * @return string
     */
    public static function format($dateTime, $blank = '', $format = 'F j, Y')
    {
        if ($dateTime instanceof DateTime) {
            return $dateTime->format($format);

        } elseif (is_string($dateTime)) {
            $object = new DateTime($dateTime);
            return $object->format($format);

        } elseif (is_integer($dateTime)) {
            $object = new DateTime();
            $object->setTimestamp($dateTime);
            return $object->format($format);
        }

        return $blank;
    }
}

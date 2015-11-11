<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

abstract class OscFormbehavior
{
    /**
     * Intercept all calls that don't have equivalents here
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     */
    public static function __callStatic($name, array $arguments)
    {
        if (class_exists('JHtmlFormbehavior')) {
            return call_user_func_array(array('JHtmlFormbehavior', $name), $arguments);
        }

        return null;
    }
}

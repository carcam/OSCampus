<?php
/**
 * @package   Oscampus
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2015-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

/**
 * Class OscampusUtilitiesArrayHelper
 *
 * @method static array  arrayUnique($myArray)
 * @method static array  fromObject($p_obj, $recurse = true, $regex = null)
 * @method static array  getColumn(&$array, $index)
 * @method static mixed  getValue(&$array, $name, $default = null, $type = '')
 * @method static array  invert($array)
 * @method static bool   isAssociative($array)
 * @method static array  pivot($source, $key = null)
 * @method static mixed  sortObjects(&$a, $k, $direction = 1, $caseSensitive = true, $locale = false)
 * @method static array  toInteger(&$array, $default = null)
 * @method static object toObject(&$array, $class = 'stdClass', $recursive = true)
 * @method static string toString($array = null, $inner_glue = '=', $outer_glue = ' ', $keepOuterKey = false)
 *
 */
abstract class OscampusUtilitiesArray
{
    public static function __callStatic($name, $arguments)
    {

        if (class_exists('\\Joomla\\Utilities\\ArrayHelper')) {
            return Joomla\Utilities\ArrayHelper::$name($arguments);

        } else {
            if ($name == 'toInteger') {
                $array   = $arguments[0];
                $default = empty($arguments[1]) ? null : $arguments[1];
                JArrayHelper::toInteger($array, $default);
                return $array;
            }

            return JArrayHelper::$name($arguments);
        }
    }
}

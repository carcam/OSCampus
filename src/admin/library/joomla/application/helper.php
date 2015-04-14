<?php
/**
 * @package   Oscampus
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

abstract class OscampusApplicationHelper extends JApplicationHelper
{
    public static function stringURLSafe($string)
    {
        if (version_compare(JVERSION, '3.0', 'lt')) {
            return JApplication::stringURLSafe($string);
        }

        return parent::stringURLSafe($string);
    }
}

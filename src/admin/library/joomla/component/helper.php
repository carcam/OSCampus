<?php
/**
 * @package   Oscampus
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2015-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

abstract class OscampusComponentHelper extends JComponentHelper
{
    public static function getParams($option = 'com_oscampus', $strict = false)
    {
        return parent::getParams($option, $strict);
    }

    public static function getComponent($option = 'com_oscampus', $strict = false)
    {
        return parent::getComponent($option, $strict);
    }
}

<?php
/**
 * @package   com_oscampus
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

abstract class OscampusModel extends JModelLegacy
{
    /**
     * @var bool
     */
    private static $pathAdded = false;

    public static function addIncludePath($path = '', $prefix = '')
    {
        return parent::addIncludePath($path, $prefix);
    }

    public static function getInstance($type, $prefix = 'OscampusModel', $config = array())
    {
        if ($prefix == 'OscampusModel' && !static::$pathAdded) {
            parent::addIncludePath(OSCAMPUS_ADMIN . '/models');
            static::$pathAdded = true;
        }
        return parent::getInstance($type, $prefix, $config);
    }
}

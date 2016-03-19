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
        return parent::getInstance($type, $prefix, $config);
    }

    /**
     * Construct a SQL where item for an access level field
     *
     * @param string $field
     * @param JUser  $user
     *
     * @return string
     */
    protected function getWhereAccess($field, JUser $user = null)
    {
        $user = $user ?: OscampusFactory::getUser();

        $accessLevels = array_unique($user->getAuthorisedViewLevels());

        return sprintf($field . ' IN (%s)', join(', ', $accessLevels));
    }
}

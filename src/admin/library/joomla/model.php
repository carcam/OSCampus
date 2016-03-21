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

    /**
     * @var string[]
     */
    protected $accessList = array();

    public static function addIncludePath($path = '', $prefix = '')
    {
        return parent::addIncludePath($path, $prefix);
    }

    public static function getInstance($type, $prefix = 'OscampusModel', $config = array())
    {
        return parent::getInstance($type, $prefix, $config);
    }

    /**
     * Provide a generic access search for selected field
     *
     * @param string         $field
     * @param JUser          $user
     *
     * @return string
     */
    protected function whereAccess($field, JUser $user = null)
    {
        $user   = $user ?: OscampusFactory::getUser();
        $userId = $user->id;

        if (!isset($this->accessList[$userId])) {
            $this->accessList[$userId] = join(', ', array_unique($user->getAuthorisedViewLevels()));
        }

        if ($this->accessList[$userId]) {
            return sprintf($field . ' IN (%s)', $this->accessList[$userId]);
        }

        return 'TRUE';
    }
}

<?php
/**
 * @package   com_oscampus
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

use Alledia\Installer\AbstractScript;

defined('_JEXEC') or die();

$includePath = __DIR__ . '/admin/library';
if (!is_dir($includePath)) {
    $includePath = __DIR__ . '/library';
}

if (is_file($includePath . '/Installer/include.php')) {
    require_once $includePath . '/Installer/include.php';
} else {
    throw new Exception('[OSCampus] Alledia Installer not found');
}

class com_oscampusInstallerScript extends AbstractScript
{
    /**
     * @param string                     $type
     * @param JInstallerComponent $parent
     *
     * @return void
     */
    public function postFlight($type, $parent)
    {
        $this->installRelated();
        $this->clearObsolete();
        $this->fixMenus();

        $this->showMessages();

        // Show additional installation messages
        $file = strpos($type, 'install') === false ? $type : 'install';
        $path = JPATH_ADMINISTRATOR . '/components/com_oscampus/views/welcome/tmpl/' . $file . '.php';
        if (is_file($path)) {
            require_once JPATH_ADMINISTRATOR . '/components/com_oscampus/include.php';
            JFactory::getLanguage()->load('com_oscampus', JPATH_ADMINISTRATOR . '/components/com_oscampus');
            require_once $path;
        }
    }
}

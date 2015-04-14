<?php
/**
 * @package   com_oscampus
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2015 Open Source Training, LLC. All rights reserved
 * @license
 */

use Alledia\Installer\AbstractScript;

defined('_JEXEC') or die();

$includePath = __DIR__ . '/admin/library';
if (!is_dir($includePath)) {
    $includePath = __DIR__ . '/library';
}

if (file_exists($includePath . '/Installer/include.php')) {
    require_once $includePath . '/Installer/include.php';
} else {
    throw new Exception('[OSCampus] Alledia Installer not found');
}

class com_oscampusInstallerScript extends AbstractScript
{
    /**
     * @var array Related extensions required or useful with the component
     *            type => [ (folder) => [ (element) => [ (publish), (uninstall), (ordering) ] ] ]
     */
    protected $relatedExtensions = array();

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
        if (file_exists($path)) {
            require_once JPATH_ADMINISTRATOR . '/components/com_oscampus/include.php';
            JFactory::getLanguage()->load('com_oscampus', JPATH_ADMINISTRATOR . '/components/com_oscampus');
            require_once $path;
        }
    }

    /**
     * Install related extensions
     * Overriding the Alledia Install because we're specifying differently
     *
     * @return void
     */
    protected function installRelated()
    {
        parent::installRelated();

        if ($this->relatedExtensions) {
            $source = $this->installer->getPath('source');

            foreach ($this->relatedExtensions as $type => $folders) {
                foreach ($folders as $folder => $extensions) {
                    foreach ($extensions as $element => $settings) {
                        $path = $source . '/' . $type;
                        if ($type == 'plugin') {
                            $path .= '/' . $folder;
                        }
                        $path .= '/' . $element;
                        if (is_dir($path)) {
                            $current = $this->findExtension($type, $element, $folder);
                            $isNew   = empty($current);

                            $typeName  = trim(($folder ?: '') . ' ' . $type);
                            $text      = 'LIB_ALLEDIAINSTALLER_RELATED_' . ($isNew ? 'INSTALL' : 'UPDATE');
                            $installer = new JInstaller();
                            if ($installer->install($path)) {
                                $this->setMessage(JText::sprintf($text, $typeName, $element));
                                if ($isNew) {
                                    $current = $this->findExtension($type, $element, $folder);
                                    if ($settings[0]) {
                                        $current->publish();
                                    }
                                    if ($settings[2] && ($type == 'plugin')) {
                                        $this->setPluginOrder($current, $settings[2]);
                                    }
                                }
                            } else {
                                $this->setMessage(JText::sprintf($text . '_FAIL', $typeName, $element), 'error');
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Uninstall the related extensions that are useless without the component
     */
    protected function uninstallRelated()
    {
        parent::uninstallRelated();

        if ($this->relatedExtensions) {
            $installer = new JInstaller();

            foreach ($this->relatedExtensions as $type => $folders) {
                foreach ($folders as $folder => $extensions) {
                    foreach ($extensions as $element => $settings) {
                        if ($settings[1]) {
                            if ($current = $this->findExtension($type, $element, $folder)) {
                                $msg     = 'LIB_ALLEDIAINSTALLER_RELATED_UNINSTALL';
                                $msgtype = 'message';
                                if (!$installer->uninstall($current->type, $current->extension_id)) {
                                    $msg .= '_FAIL';
                                    $msgtype = 'error';
                                }
                                $this->setMessage(JText::sprintf($msg, $type, $element), $msgtype);
                            }
                        }
                    }
                }
            }
        }
    }
}

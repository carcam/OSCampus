<?php
/**
 * @package   com_oscampus
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

abstract class OscampusHelper
{
    /**
     * get component information
     *
     * @return JRegistry
     */
    public static function getInfo()
    {
        $info = new jRegistry();
        $path = OSCAMPUS_ADMIN . '/oscampus.xml';
        if (file_exists($path)) {
            $xml = JFactory::getXML($path);

            foreach ($xml->children() as $e) {
                if (!$e->children()) {
                    $info->set($e->getName(), (string)$e);
                }
            }
        }
        return $info;
    }

    /**
     * Get the requested application object
     *
     * @param $client
     *
     * @return JApplication
     */
    public static function getApplication($client)
    {
        if (version_compare(JVERSION, '3.0', 'lt')) {
            return JApplication::getInstance($client);
        }

        return JApplicationCms::getInstance($client);
    }

    /**
     * Render the modules in a position
     *
     * @param string $position
     * @param mixed  $attribs
     *
     * @return string
     */
    public static function renderModule($position, $attribs = array())
    {
        $results = JModuleHelper::getModules($position);
        $content = '';

        ob_start();
        foreach ($results as $result) {
            $content .= JModuleHelper::renderModule($result, $attribs);
        }
        ob_end_clean();

        return $content;
    }

    /**
     * Make sure the appropriate component language files are loaded
     *
     * @param string $option
     * @param string $adminPath
     * @param string $sitePath
     *
     * @return void
     * @throws Exception
     */
    public static function loadOptionLanguage($option, $adminPath, $sitePath)
    {
        $app = OscampusFactory::getApplication();
        if ($app->input->getCmd('option') != $option) {
            switch (JFactory::getApplication()->getName()) {
                case 'administrator':
                    OscampusFactory::getLanguage()->load($option, $adminPath);
                    break;

                case 'site':
                    OscampusFactory::getLanguage()->load($option, $sitePath);
                    break;
            }
        }
    }
}

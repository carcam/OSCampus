<?php
/**
 * @package   com_oscampus
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

use Joomla\Registry\Registry as Registry;

defined('_JEXEC') or die();

abstract class OscampusHelper
{
    /**
     * get component information
     *
     * @return Registry
     */
    public static function getInfo()
    {
        $info = new Registry();
        $path = OSCAMPUS_ADMIN . '/oscampus.xml';
        if (is_file($path)) {
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

    /**
     * Check a potentially local, relative url and make sure
     * it is properly formed
     *
     * @param string $url
     *
     * @return string
     */
    public static function normalizeUrl($url)
    {
        if (!preg_match('#https?://#', $url)) {
            $root = OscampusFactory::getURI()->root(true);

            return rtrim($root, '/') . '/' . ltrim($url, '/');
        }

        return $url;
    }
}

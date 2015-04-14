<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

use Oscampus\AutoLoader;

defined('_JEXEC') or die();

if (!defined('OSCAMPUS_LOADED')) {
    define('OSCAMPUS_LOADED', 1);
    define('OSCAMPUS_ADMIN', JPATH_ADMINISTRATOR . '/components/com_oscampus');
    define('OSCAMPUS_SITE', JPATH_SITE . '/components/com_oscampus');
    define('OSCAMPUS_MEDIA', JPATH_SITE . '/media/com_oscampus');
    define('OSCAMPUS_LIBRARY', OSCAMPUS_ADMIN . '/library');

    // Setup autoload libraries
    require_once OSCAMPUS_LIBRARY . '/oscampus/AutoLoader.php';
    AutoLoader::register('Oscampus', OSCAMPUS_LIBRARY . '/oscampus');
    AutoLoader::register('Pimple', OSCAMPUS_LIBRARY . '/pimple');

    AutoLoader::registerCamelBase('Oscampus', OSCAMPUS_LIBRARY . '/joomla');

    // Any additional helper paths
    JHtml::addIncludePath(OSCAMPUS_LIBRARY . '/html');
    OscampusHelper::loadOptionLanguage('com_oscampus', OSCAMPUS_ADMIN, OSCAMPUS_SITE);
}

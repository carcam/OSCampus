<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Oscampus\AutoLoader;

defined('_JEXEC') or die();

if (!defined('OSCAMPUS_LOADED')) {
    define('OSCAMPUS_LOADED', 1);
    define('OSCAMPUS_ADMIN', JPATH_ADMINISTRATOR . '/components/com_oscampus');
    define('OSCAMPUS_SITE', JPATH_SITE . '/components/com_oscampus');
    define('OSCAMPUS_MEDIA', JPATH_SITE . '/media/com_oscampus');
    define('OSCAMPUS_LIBRARY', OSCAMPUS_ADMIN . '/library');

    // Include vendor dependencies
    require_once OSCAMPUS_ADMIN . '/vendor/autoload.php';

    // Setup autoload libraries
    require_once OSCAMPUS_LIBRARY . '/oscampus/AutoLoader.php';
    AutoLoader::register('Oscampus', OSCAMPUS_LIBRARY . '/oscampus');
    AutoLoader::registerCamelBase('Oscampus', OSCAMPUS_LIBRARY . '/joomla');

    // Any additional helper paths
    JHtml::addIncludePath(OSCAMPUS_LIBRARY . '/html');
    OscampusHelper::loadOptionLanguage('com_oscampus', OSCAMPUS_ADMIN, OSCAMPUS_SITE);

    // Application specific loads
    switch (OscampusFactory::getApplication()->getName()) {
        case 'site':
            OscampusModel::addIncludePath(OSCAMPUS_SITE . '/models');
            break;

        case 'administrator':
            OscampusModel::addIncludePath(OSCAMPUS_ADMIN . '/models');
            break;
    }
}

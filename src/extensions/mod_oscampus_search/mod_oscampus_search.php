<?php
/**
 * @package    OSCampus
 * @subpackage OSCampus Search Module
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

use Joomla\Registry\Registry as Registry;

use Oscampus\Module\Search;

defined('_JEXEC') or die();

if (!defined('OSCAMPUS_LOADED')) {
    $path = JPATH_ADMINISTRATOR . '/components/com_oscampus/include.php';
    if (is_file($path)) {
        require_once $path;
    }
}

if (defined('OSCAMPUS_LOADED')) {
    Oscampus\AutoLoader::register('Oscampus', __DIR__ . '/oscampus');

    /** @var Registry $params */
    $module = new Search($params, $module);
    $module->addScript();
    $module->output();
}

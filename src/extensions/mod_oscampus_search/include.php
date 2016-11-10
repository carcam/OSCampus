<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

if (!defined('OSCAMPUS_LOADED')) {
    JLoader::import('include', JPATH_ADMINISTRATOR . '/components/com_oscampus');
}

if (!defined('OSCAMPUS_LOADED')) {
    throw new Exception(JText::_('MOD_OSCAMPUS_SEARCH_ERROR_INSTALL_OSCAMPUS'), 500);
}

Oscampus\AutoLoader::register('Oscampus', __DIR__ . '/oscampus');

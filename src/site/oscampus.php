<?php
/**
 * @package   com_oscampus
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2015-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

require_once JPATH_COMPONENT_ADMINISTRATOR . '/include.php';

OscampusHelperSite::loadTheme();

$controller = OscampusControllerBase::getInstance('Oscampus');
$controller->execute(JFactory::getApplication()->input->getCmd('task'));
$controller->redirect();

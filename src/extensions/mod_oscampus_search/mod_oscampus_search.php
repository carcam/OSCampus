<?php
/**
 * @package    OSCampus
 * @subpackage OSCampus Search Module
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Joomla\Registry\Registry as Registry;

use Oscampus\Module\Search;

defined('_JEXEC') or die();

/**
 * @var JApplicationSite $app
 * @var string[]         $attribs
 * @var bool[]           $chrome
 * @var string           $content
 * @var JLanguage        $lang
 * @var object           $module
 * @var Registry         $params
 * @var string           $path
 * @var mixed            $scope
 * @var string           $template
 */

require_once __DIR__ . '/include.php';

$view = new Search($params, $module);
$view->output();

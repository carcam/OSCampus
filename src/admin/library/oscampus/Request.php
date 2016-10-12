<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Oscampus;

use JFactory;

defined('_JEXEC') or die();

abstract class Request
{
    /**
     * Check if the current request is an ajax request or a normal one.
     *
     * @return bool
     */
    public static function isAjax()
    {
        $requestedWith = strtolower(JFactory::getApplication()->input->server->get('HTTP_X_REQUESTED_WITH'));

        return $requestedWith === 'xmlhttprequest';
    }
}

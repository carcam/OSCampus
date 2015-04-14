<?php
/**
 * @package   Oscampus
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Oscampus\Exception;

use Oscampus\Exception;

defined('_JEXEC') or die();

class NotFound extends Exception
{
    public function __construct($message = "", $code = 404, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

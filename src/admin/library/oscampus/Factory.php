<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */
namespace Oscampus;

use Oscampus\Lesson\AbstractLesson;

defined('_JEXEC') or die();

abstract class Factory
{
    public static function getLesson($data)
    {
        if (is_object($data)) {
            $data = get_object_vars($data);
        }

        if (is_array($data)) {
            return AbstractLesson::getInstance($data);
        }

        return null;
    }
}

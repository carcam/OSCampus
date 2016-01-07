<?php
/**
 * @package   Oscampus
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2015-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Oscampus\Container;
use Oscampus\Services;

defined('_JEXEC') or die();

abstract class OscampusFactory extends JFactory
{
    /**
     * @var Container
     */
    protected static $container = null;

    /**
     * @return Container
     */
    public static function getContainer()
    {
        if (static::$container === null) {
            static::$container = new Container();
            static::$container->register(new Services());

        }
        return static::$container;
    }

}

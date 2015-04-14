<?php
/**
 * @package   Oscampus
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Oscampus\Cms\Joomla\Services\Oscampus;
use Oscampus\Container;

defined('_JEXEC') or die();

abstract class OscampusFactory extends JFactory
{
    /**
     * @var array
     */
    protected static $OscampusContainer = null;

    /**
     * Get the Oscampus container class
     *
     * @param JRegistry $params
     *
     * @return Container
     * @throws Exception
     */
    public static function getContainer(JRegistry $params = null)
    {
        if (static::$OscampusContainer === null) {
            $container = new Container();
            $services  = new Oscampus(array());
            $container->register($services);

            static::$OscampusContainer = $container;
        }
        return static::$OscampusContainer;
    }
}

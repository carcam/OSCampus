<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

namespace Oscampus;

use Pimple\Container AS Pimple;
use Pimple\ServiceProviderInterface;

defined('_JEXEC') or die();

/**
 * Class Services
 *
 * @package Oscampus
 */
class Services implements ServiceProviderInterface
{

    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Pimple $pimple A container instance
     */
    public function register(Pimple $pimple)
    {
        $pimple['lesson'] = $pimple->factory(function (Container $c) {
            return new Lesson();
        });
    }
}

<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

namespace Oscampus;

use Mobile_Detect;
use Oscampus\Lesson;
use Oscampus\Lesson\Properties;
use Oscampus\UserActivity;
use OscampusFactory;
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
        $pimple['dbo'] = function (Container $c) {
            return OscampusFactory::getDbo();
        };

        $pimple['lesson'] = $pimple->factory(
            function (Container $c) {
                $properties = new Properties();
                return new Lesson($c['dbo'], $properties);
            }
        );

        $pimple['device'] = function () {
            return new Mobile_Detect();
        };

        $pimple['activity'] = $pimple->factory(
            function (Container $c) {
                return new UserActivity($c['dbo']);
            }
        );
    }
}

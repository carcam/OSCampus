<?php
/**
 * @package   Oscampus
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Oscampus\Cms\Joomla\Services;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Oscampus\Api\Account;
use Oscampus\Api\Billing;
use Oscampus\Api\Coupon;
use Oscampus\Api\Invoice;
use Oscampus\Api\Plan;
use Oscampus\Api\Subscription;
use Oscampus\Api\Transaction;
use Oscampus\Configuration;
use Oscampus\Notify\Notify;
use Oscampus\Plugin\Events;
use Oscampus\User\User;

defined('_JEXEC') or die();

class Oscampus implements ServiceProviderInterface
{
    /**
     * @var array
     */
    protected $config = null;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Registers services on the given container.
     * Note that this instance expects to be registered
     * with Oscampus\Container, an overloaded version
     * of Pimple\Container
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $pimple A Container instance
     * @param array     $config Configuration array
     */
    public function register(Container $pimple, array $config = array())
    {
        // Parameters
        $pimple['configData']       = $this->config;
        $pimple['cmsNamespace']     = 'Oscampus\Cms\Joomla';

        // Services
        $pimple['configuration'] = function (\Oscampus\Container $c) {
            return new Configuration($c['configData']);
        };

        // User classes
        $pimple['userAdapter'] = function (\Oscampus\Container $c) {
            $adapter = $c['cmsNamespace'] . '\User\UserAdapter';
            return new $adapter();
        };

        $pimple['user'] = $pimple->factory(function (\Oscampus\Container $c) {
            return new User($c['configuration'], $c['userAdapter']);
        });
    }
}

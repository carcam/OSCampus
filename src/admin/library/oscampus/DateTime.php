<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

namespace Oscampus;

defined('_JEXEC') or die();

class DateTime extends \DateTime
{
    public function __construct($time = 'now', \DateTimeZone $timezone = null)
    {
        $timezone = $timezone ?: new \DateTimeZone('utc');

        parent::__construct($time, $timezone);
    }
}

<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

namespace Oscampus\Activity;

use DateTime;
use Oscampus\AbstractPrototype;

defined('_JEXEC') or die();

class CourseStatus extends AbstractPrototype
{
    /**
     * @var int
     */
    public $id              = null;

    /**
     * @var int
     */
    public $users_id        = null;

    /**
     * @var int
     */
    public $certificates_id = null;

    /**
     * @var string
     */
    public $title           = null;

    /**
     * @var int
     */
    public $lessons         = null;

    /**
     * @var int
     */
    public $lessons_taken   = null;

    /**
     * @var DateTime
     */
    public $first_visit     = null;

    /**
     * @var DateTime
     */
    public $last_visit      = null;

    /**
     * @var string
     */
    public $scores          = null;

    /**
     * @var DateTime
     */
    public $date_earned     = null;

    protected $dateProperties = array(
        'first_visit',
        'last_visit',
        'date_earned'
    );
}

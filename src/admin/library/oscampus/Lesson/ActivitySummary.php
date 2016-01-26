<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

namespace Oscampus\Lesson;

use DateTime;
use Oscampus\AbstractPrototype;

defined('_JEXEC') or die();

class ActivitySummary extends AbstractPrototype
{
    /**
     * @var int
     */
    public $id = null;

    /**
     * @var int
     */
    public $users_id = null;

    /**
     * @var int
     */
    public $lessons = null;

    /**
     * @var int
     */
    public $viewed = null;

    /**
     * @var int
     */
    public $visits = null;

    /**
     * @var DateTime
     */
    public $completed = null;

    /**
     * @var DateTime
     */
    public $certificate = null;

    /**
     * @var DateTime
     */
    public $first_visit = null;

    /**
     * @var DateTime
     */
    public $last_visit = null;

    protected $dateProperties = array(
        'completed',
        'certificate',
        'first_visit',
        'last_visit'
    );
}

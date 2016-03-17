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

/**
 * Class CourseStatus
 *
 * @package Oscampus\Activity
 *
 * @property-read float $progress
 */
class CourseStatus extends AbstractPrototype
{
    // Completion statuses (stati?)
    const NOT_STARTED = 0;
    const IN_PROGRESS = 1;
    const COMPLETED   = 2;

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
    public $certificates_id = null;

    /**
     * @var string
     */
    public $title = null;

    /**
     * @var int
     */
    public $lesson_count = null;

    /**
     * @var int
     */
    public $lessons_taken = null;

    /**
     * @var DateTime
     */
    public $first_visit = null;

    /**
     * @var DateTime
     */
    public $last_visit = null;

    /**
     * @var int
     */
    public $last_lesson = null;

    /**
     * @var string
     */
    public $scores = null;

    /**
     * @var DateTime
     */
    public $date_earned = null;

    protected $dateProperties = array(
        'first_visit',
        'last_visit',
        'date_earned'
    );

    public function __get($name)
    {
        if ($name == 'progress' && $this->lesson_count > 0) {
            return round(($this->lessons_taken / $this->lesson_count) * 100, 0);
        }

        return null;
    }
}

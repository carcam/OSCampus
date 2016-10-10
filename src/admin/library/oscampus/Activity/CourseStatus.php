<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
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
 * @property-read int   $courses_id
 */
class CourseStatus extends AbstractPrototype
{
    // Progress statuses (stati?)
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
    public $lessons_viewed = null;

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
        switch ($name) {
            case 'courses_id':
                return $this->id;
                break;

            case 'progress':
                if ($this->lesson_count > 0) {
                    return round(($this->lessons_viewed / $this->lesson_count) * 100, 0);
                }
                return 0;
                break;

        }

        return null;
    }
}

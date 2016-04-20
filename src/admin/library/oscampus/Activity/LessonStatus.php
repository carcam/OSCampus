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

class LessonStatus extends AbstractPrototype
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
    public $lessons_id = null;

    /**
     * @var int
     */
    public $courses_id = null;

    /**
     * @var string
     */
    public $type = null;

    /**
     * @var DateTime
     */
    public $completed = null;

    /**
     * @var int
     */
    public $score = 0;

    /**
     * @var int
     */
    public $visits = 0;

    /**
     * @var mixed
     */
    public $data = null;

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
        'last_visit',
        'first_visit'
    );
}

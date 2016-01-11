<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

namespace Oscampus\Lesson\Type;

use Oscampus\Lesson;
use OscampusFactory;

defined('_JEXEC') or die();

abstract class AbstractType
{
    /**
     * @var Lesson
     */
    protected $lesson = null;

    public function __construct(Lesson $lesson)
    {
        $this->lesson = $lesson;
    }

    abstract public function render();

    /**
     * get the current user state from a cookie
     *
     * @param string $name
     * @param string $default
     *
     * @return string
     */
    public function getUserState($name, $default = null)
    {
        return OscampusFactory::getApplication()
            ->input
            ->cookie
            ->getString($name, $default);
    }

    /**
     * Save a user state in a session cookie, returning the original value
     *
     * @param string $name
     * @param string $value
     *
     * @return string
     */
    public function setUserState($name, $value)
    {
        $oldValue = $this->getUserState($name);

        setcookie($name, $value);

        return $oldValue;
    }

    /**
     * Prepare an activity record for recording user progress. This is
     * the default behavior. Subclasses should override as needed for
     * their specialized purposes.
     *
     * @param object $activity
     * @param float  $score
     * @param mixed  $data
     * @param bool   $updateLastVisitTime
     *
     * @return void
     */
    public function prepareActivityProgress($activity, $score, $data, $updateLastVisitTime = true)
    {
        if ($activity->score < $score) {
            $activity->score = $score;
        }

        $now = OscampusFactory::getDate()->toSql();
        if ($activity->score >= 100) {
            $activity->completed = $now;
        }
        if ($updateLastVisitTime) {
            $activity->last_visit = $now;
        }

        $activity->data  = $data;
    }

    public function __toString()
    {
        return get_class($this);
    }
}

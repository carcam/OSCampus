<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

namespace Oscampus\Lesson\Type;

use Exception;
use JRegistry;
use Oscampus\Activity\LessonStatus;
use Oscampus\Lesson;
use OscampusFactory;
use SimpleXMLElement;

defined('_JEXEC') or die();

abstract class AbstractType
{
    const PASSING_SCORE = 0;

    /**
     * @var Lesson
     */
    protected $lesson = null;

    public function __construct(Lesson $lesson)
    {
        $this->lesson = $lesson;
    }

    /**
     * Each lesson type must provide the output, loading of
     * js, etc needed for their particular needs
     *
     * @return string
     */
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
     * Prepare an LessonStatus for recording user progress.
     *
     * @param LessonStatus $status
     * @param int          $score
     * @param mixed        $data
     *
     * @return void
     */
    abstract public function prepareActivityProgress(LessonStatus $status, $score = null, $data = null);

    /**
     * Prepare data and provide XML for use in lesson admin UI.
     *
     * @param JRegistry $data
     *
     * @return SimpleXMLElement
     */
    abstract public function prepareAdminData(JRegistry $data);

    /**
     * The default procedure to vet the lesson content on saving
     * changes in admin. Note passing of data object allowing
     * modification of any of the form POST data
     *
     * @param JRegistry $data
     *
     * @throws Exception
     */
    public function saveAdminChanges(JRegistry $data)
    {
        // Subclasses indicate a problem by throwing Exception
    }

    public function __toString()
    {
        return get_class($this);
    }
}

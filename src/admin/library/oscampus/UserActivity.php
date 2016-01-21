<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

namespace Oscampus;

use JDatabase;
use JUser;
use Oscampus\Lesson\ActivityStatus;
use OscampusFactory;

defined('_JEXEC') or die();

class UserActivity extends AbstractBase
{
    /**
     * @var JUser
     */
    protected $user = null;

    /**
     * @var ActivityStatus
     */
    protected $status = null;

    /**
     * @var array[]
     */
    protected $lessons = array();

    public function __construct(JDatabase $dbo, JUser $user, ActivityStatus $activityStatus)
    {
        parent::__construct($dbo);

        $this->user   = $user;
        $this->status = $activityStatus;
    }

    /**
     * Change to the selected user's tracking data
     *
     * @param int $id
     *
     * @return JUser
     */
    public function setUser($id)
    {
        if ($id != $this->user->id) {
            $this->user->load($id);
            $this->lessons = array();
        }

        return $this->user;
    }

    /**
     * Internal method for loading activity records for the currently set user
     *
     * @param $courseId
     *
     * @return array
     */
    protected function get($courseId)
    {
        if ($this->user->id) {
            if (!isset($this->lessons[$courseId])) {
                $query = $this->dbo->getQuery(true)
                    ->select('ul.*, module.courses_id')
                    ->from('#__oscampus_lessons lesson')
                    ->innerJoin('#__oscampus_modules module ON module.id = lesson.modules_id')
                    ->leftJoin('#__oscampus_users_lessons ul ON ul.lessons_id = lesson.id')
                    ->where(
                        array(
                            'ul.users_id = ' . $this->user->id,
                            'module.courses_id = ' . (int)$courseId
                        )
                    )
                    ->order('module.ordering ASC, lesson.ordering ASC');

                $this->lessons[$courseId] = $this->dbo->setQuery($query)->loadObjectList('lessons_id');
            }
        }

        return $this->lessons[$courseId];
    }

    /**
     * Get the course ID from the lesson ID since lessons doesn't
     * provide this information directly
     *
     * @param int $lessonId
     *
     * @return int
     */
    protected function getCourseIdFromLessonId($lessonId)
    {

        $query = $this->dbo->getQuery(true)
            ->select('courses_id')
            ->from('#__oscampus_modules m1')
            ->innerJoin('#__oscampus_lessons l1 ON l1.modules_id = m1.id')
            ->where('l1.id = ' . (int)$lessonId);

        return $this->dbo->setQuery($query)->loadColumn();
    }

    /**
     * Get all activity records for this user for the selected course
     *
     * @param int $courseId
     *
     * @return object[]
     */
    public function getCourse($courseId)
    {
        $lessons = $this->get($courseId);
        return $lessons;
    }

    /**
     * Get tracking data for the current user in the selected lesson.
     * If the course ID is already known, there is a small performance
     * improvement if it is provided also.
     *
     * @param int $lessonId
     * @param int $courseId
     *
     * @return object|null
     */
    public function getLesson($lessonId, $courseId = null)
    {
        $courseId = $courseId ?: $this->getCourseIdFromLessonId($lessonId);
        $lessons  = $this->get($courseId);
        if (empty($lessons[$lessonId])) {
            return null;
        }

        return $lessons[$lessonId];
    }

    /**
     * Update last visit date and number of visits
     *
     * @param int $lessonId
     */
    public function visitLesson($lessonId)
    {
        if ($this->user->id) {
            $app = OscampusFactory::getApplication();

            $activity = $this->getStatus($lessonId);

            // Always record the current time
            $activity->last_visit = OscampusFactory::getDate()->toSql();

            // Don't bump the visit count if the page is only refreshing
            $visited = $app->getUserState('oscampus.lesson.visited');
            if ($activity->id && $visited != $lessonId) {
                $activity->visits++;
                $app->setUserState('oscampus.lesson.visited', $lessonId);
            }

            $this->setStatus($activity);
        }
    }

    /**
     * Insert/Update user activity record
     *
     * @param Lesson $lesson
     * @param int    $score
     * @param null   $data
     * @param bool   $updateLastVisitTime
     */
    public function recordProgress(Lesson $lesson, $score = 100, $data = null, $updateLastVisitTime = true)
    {
        $activity = $this->getStatus($lesson->id);
        $lesson->renderer->prepareActivityProgress($activity, $score, $data, $updateLastVisitTime);

        $this->setStatus($activity);
    }

    /**
     * Get an activity status record
     *
     * @param int $lessonId
     * @param int $userId
     *
     * @return object
     */
    public function getStatus($lessonId, $userId = null)
    {
        $userId = $userId ?: $this->user->id;
        if ($userId) {
            $query  = $this->dbo->getQuery(true)
                ->select('*')
                ->from('#__oscampus_users_lessons')
                ->where(
                    array(
                        'users_id = ' . (int)$userId,
                        'lessons_id = ' . (int)$lessonId
                    )
                );
            $status = $this->dbo->setQuery($query)->loadObject(get_class($this->status));
        }

        if (empty($status)) {
            $status = clone $this->status;
            $status->setProperties(
                array(
                    'users_id'   => $userId,
                    'lessons_id' => $lessonId
                )
            );
        }

        return $status;
    }

    /**
     * insert/update an activity status record
     *
     * @param array|object $status
     *
     * @return bool
     */
    public function setStatus($status)
    {
        if (is_array($status)) {
            $status = (object)$status;
        }

        if (is_object($status) && !empty($status->users_id) && !empty($status->lessons_id)) {
            $thisVisit = OscampusFactory::getDate()->toSql();

            if (empty($status->id)) {
                $status->first_visit = $thisVisit;
                $status->last_visit  = $thisVisit;
                $status->visits      = 1;

                $success = $this->dbo->insertObject('#__oscampus_users_lessons', $status);

            } else {
                $success = $this->dbo->updateObject('#__oscampus_users_lessons', $status, 'id');
            }
            return $success;
        }

        return false;
    }
}

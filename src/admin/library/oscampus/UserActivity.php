<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

namespace Oscampus;

use JDatabaseQuery;
use JDatabase;
use JUser;
use Oscampus\Lesson\ActivityStatus;
use Oscampus\Lesson\ActivitySummary;
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
     * @var ActivitySummary
     */
    protected $summary = null;

    /**
     * @var Certificate
     */
    public $certificate = null;

    /**
     * @var array[]
     */
    protected $lessons = array();

    public function __construct(
        JDatabase $dbo,
        JUser $user,
        ActivityStatus $activityStatus,
        ActivitySummary $activitySummary,
        Certificate $certificate
    ) {
        parent::__construct($dbo);

        $this->user        = $user;
        $this->status      = $activityStatus;
        $this->summary     = $activitySummary;
        $this->certificate = $certificate;
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
     * @return ActivityStatus[]
     */
    protected function get($courseId)
    {
        if ($this->user->id) {
            if (!isset($this->lessons[$courseId])) {
                $query = $this->getStatusQuery()
                    ->where('module.courses_id = ' . (int)$courseId);

                $this->lessons[$courseId] = $this->dbo
                    ->setQuery($query)
                    ->loadObjectList('lessons_id', get_class($this->status));
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
     * @return ActivityStatus[]
     */
    public function getCourse($courseId)
    {
        $lessons = $this->get($courseId);
        return $lessons;
    }

    /**
     * Update last visit date and number of visits
     *
     * @param Lesson $lesson
     */
    public function visitLesson($lesson)
    {
        if ($this->user->id) {
            $app = OscampusFactory::getApplication();

            $status = $this->getStatus($lesson->id);

            // Always record the current time
            $status->last_visit = OscampusFactory::getDate()->toSql();

            // Don't bump the visit count if the page is only refreshing
            $visited = $app->getUserState('oscampus.lesson.visited');
            if ($status->id && $visited != $lesson->id) {
                $status->visits++;
            }

            $this->recordProgress($lesson);
            $app->setUserState('oscampus.lesson.visited', $lesson->id);
        }
    }

    /**
     * Insert/Update user activity record
     *
     * @param Lesson $lesson
     * @param int    $score
     * @param null   $data
     */
    public function recordProgress(Lesson $lesson, $score = null, $data = null)
    {
        $status    = $this->getStatus($lesson->id);
        $completed = $status->completed;

        $lesson->renderer->prepareActivityProgress($status, $score, $data);
        $this->setStatus($status);

        // On transition to completed, check to see if they earned a certificate
        if (!$completed && $status->completed) {
            $this->certificate->award($status->courses_id, $this);
        }

    }

    /**
     * Get an activity status record
     *
     * @param int $lessonId
     * @param int $courseId
     * @param int $userId
     *
     * @return ActivityStatus
     */
    public function getStatus($lessonId, $courseId = null, $userId = null)
    {
        $userId = $userId ?: $this->user->id;
        if ($userId) {
            $query = $this->getStatusQuery()
                ->where('lesson.id = ' . (int)$lessonId);

            $status = $this->dbo->setQuery($query)->loadObject(get_class($this->status));
        }

        if (empty($status)) {
            $status = clone $this->status;
        }
        if (!$status->users_id) {
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
     * Standard query for finding status records. We're doing this so
     * that we can pull all activity records for a course by filtering
     * on module.courses_id when needed
     *
     * @return JDatabaseQuery
     */
    protected function getStatusQuery()
    {
        $userId = $this->user->id;

        $query = $this->dbo->getQuery(true)
            ->select('activity.*, module.courses_id, lesson.type')
            ->from('#__oscampus_lessons lesson')
            ->innerJoin('#__oscampus_modules AS module ON module.id = lesson.modules_id')
            ->leftJoin('#__oscampus_users_lessons AS activity ON activity.lessons_id = lesson.id AND activity.users_id = ' . $userId)
            ->order('module.ordering ASC, lesson.ordering ASC');

        return $query;
    }

    /**
     * insert/update an activity status record
     *
     * @param ActivityStatus $status
     *
     * @return bool
     */
    public function setStatus(ActivityStatus $status)
    {
        if (!empty($status->users_id) && !empty($status->lessons_id)) {
            $fields = $this->dbo->getTableColumns('#__oscampus_users_lessons');

            if (empty($status->id)) {
                $thisVisit = OscampusFactory::getDate();

                $status->first_visit = $thisVisit;
                $status->last_visit  = $thisVisit;
                $status->visits      = 1;

                $insert  = (object)array_intersect_key($status->toArray(), $fields);
                $success = $this->dbo->insertObject('#__oscampus_users_lessons', $insert);

            } else {
                $update  = (object)array_intersect_key($status->toArray(), $fields);
                $success = $this->dbo->updateObject('#__oscampus_users_lessons', $update, 'id');
            }
            return $success;
        }

        return false;
    }

    /**
     * Get a summary of this user's activity for courses
     *
     * @param int $courseId
     *
     * @return ActivitySummary|ActivitySummary[]
     */
    public function summary($courseId = null)
    {
        $queryCount = $this->dbo->getQuery(true)
            ->select('m1.courses_id, count(distinct l1.id) lessons')
            ->from('#__oscampus_lessons AS l1')
            ->innerJoin('#__oscampus_modules AS m1 ON m1.id = l1.modules_id')
            ->group('m1.courses_id');

        $query = $this->dbo->getQuery(true)
            ->select(
                array(
                    'course.id',
                    'activity.users_id',
                    'lcount.lessons',
                    'count(DISTINCT activity.lessons_id) AS viewed',
                    'SUM(DISTINCT activity.visits) AS visits',
                    'MAX(activity.completed) AS completed',
                    'certificate.date_earned AS certificate',
                    'MIN(activity.first_visit) AS first_visit',
                    'MAX(activity.last_visit) AS last_visit'
                )
            )
            ->from('#__oscampus_users_lessons AS activity')
            ->leftJoin('#__oscampus_lessons AS lesson ON lesson.id = activity.lessons_id')
            ->leftJoin('#__oscampus_modules AS module ON module.id = lesson.modules_id')
            ->leftJoin('#__oscampus_courses AS course ON course.id = module.courses_id')
            ->leftJoin('#__oscampus_certificates AS certificate ON certificate.users_id = activity.users_id AND certificate.courses_id = course.id')
            ->leftJoin("({$queryCount}) AS lcount ON lcount.courses_id = course.id")
            ->where('activity.users_id = ' . $this->user->id)
            ->group('activity.users_id, module.courses_id');

        if ($courseId) {
            $query->where('course.id = ' . (int)$courseId);
        }

        $summary = $this->dbo->setQuery($query)->loadObjectlist('id', get_class($this->summary));

        if ($courseId) {
            return array_pop($summary);
        }

        return $summary;
    }
}

<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

namespace Oscampus;

use JDatabaseDriver;
use JDatabaseQuery;
use JUser;
use Oscampus\Activity\CourseStatus;
use Oscampus\Activity\LessonStatus;
use Oscampus\Activity\LessonSummary;
use OscampusFactory;

defined('_JEXEC') or die();

class UserActivity extends AbstractBase
{
    /**
     * @var JUser
     */
    protected $user = null;

    /**
     * @var LessonStatus
     */
    protected $lessonStatus = null;

    /**
     * @var LessonStatus[]
     */
    protected $lessons = array();

    /**
     * @var LessonSummary
     */
    protected $lessonSummary = null;

    /**
     * @var Certificate
     */
    public $certificate = null;

    /**
     * @var CourseStatus
     */
    protected $courseStatus = null;

    /**
     * @var CourseStatus[]
     */
    protected $courses = null;

    public function __construct(
        JDatabaseDriver $dbo,
        JUser $user,
        LessonStatus $lessonStatus,
        LessonSummary $lessonSummary,
        CourseStatus $courseStatus,
        Certificate $certificate
    ) {
        parent::__construct($dbo);

        $this->user          = $user;
        $this->lessonStatus  = $lessonStatus;
        $this->lessonSummary = $lessonSummary;
        $this->courseStatus  = $courseStatus;
        $this->certificate   = $certificate;
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
            $this->courses = array();
        }

        return $this->user;
    }

    /**
     * Internal method for loading activity records for the currently set user
     *
     * @param $courseId
     *
     * @return LessonStatus[]
     */
    protected function get($courseId)
    {
        if ($this->user->id) {
            if (!isset($this->lessons[$courseId])) {
                $query = $this->getStatusQuery()
                    ->where('module.courses_id = ' . (int)$courseId);

                $this->lessons[$courseId] = $this->dbo
                    ->setQuery($query)
                    ->loadObjectList('lessons_id', get_class($this->lessonStatus));

                if (version_compare(phpversion(), '5.6.21', 'ge')) {
                    /** @var LessonStatus $course */
                    foreach ($this->lessons[$courseId] as $course) {
                        $course->setProperties($course->toArray());
                    }
                }
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
     * @return LessonStatus[]
     */
    public function getCourseLessons($courseId)
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

            $lessonStatus = $this->getLessonStatus($lesson->id);

            // Always record the current time
            $lessonStatus->last_visit = OscampusFactory::getDate()->toSql();

            // Don't bump the visit count if the page is only refreshing
            $visited = $app->getUserState('oscampus.lesson.visited');
            if ($lessonStatus->id && $visited != $lesson->id) {
                $lessonStatus->visits++;
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
        $lessonStatus = $this->getLessonStatus($lesson->id);
        $completed    = $lessonStatus->completed;

        $lesson->renderer->prepareActivityProgress($lessonStatus, $score, $data);
        $this->setStatus($lessonStatus);

        // On transition to completed, check to see if they earned a certificate
        if (!$completed && $lessonStatus->completed) {
            $this->certificate->award($lessonStatus->courses_id, $this);
        }
    }

    /**
     * Get an activity status record
     *
     * @param int $lessonId
     *
     * @return LessonStatus
     */
    public function getLessonStatus($lessonId)
    {
        if ($this->user->id) {
            $query = $this->getStatusQuery()
                ->where('lesson.id = ' . (int)$lessonId);

            $lessonStatus = $this->dbo->setQuery($query)->loadObject(get_class($this->lessonStatus));
        }

        if (empty($lessonStatus)) {
            $lessonStatus = clone $this->lessonStatus;
        }
        if (!$lessonStatus->users_id) {
            $lessonStatus->setProperties(
                array(
                    'users_id'   => $this->user->id,
                    'lessons_id' => $lessonId
                )
            );
        }

        return $lessonStatus;
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
            ->where('lesson.published = 1')
            ->order('module.ordering ASC, lesson.ordering ASC');

        return $query;
    }

    /**
     * insert/update an activity status record
     *
     * @param LessonStatus $lessonStatus
     *
     * @return bool
     */
    public function setStatus(LessonStatus $lessonStatus)
    {
        if (!empty($lessonStatus->users_id) && !empty($lessonStatus->lessons_id)) {
            $fields = $this->dbo->getTableColumns('#__oscampus_users_lessons');

            if (empty($lessonStatus->id)) {
                $thisVisit = OscampusFactory::getDate();

                $lessonStatus->first_visit = $thisVisit;
                $lessonStatus->last_visit  = $thisVisit;
                $lessonStatus->visits      = 1;

                $insert  = (object)array_intersect_key($lessonStatus->toArray(), $fields);
                $success = $this->dbo->insertObject('#__oscampus_users_lessons', $insert);

            } else {
                $update  = (object)array_intersect_key($lessonStatus->toArray(), $fields);
                $success = $this->dbo->updateObject('#__oscampus_users_lessons', $update, 'id');
            }
            return $success;
        }

        return false;
    }

    /**
     * Get a summary of this user's lesson activity for courses
     *
     * @param int $courseId
     *
     * @return LessonSummary[]
     */
    public function getLessonSummary($courseId = null)
    {
        $queryCount = $this->dbo->getQuery(true)
            ->select('m1.courses_id, count(distinct l1.id) lessons')
            ->from('#__oscampus_lessons AS l1')
            ->innerJoin('#__oscampus_modules AS m1 ON m1.id = l1.modules_id')
            ->where('l1.published = 1')
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

        $summary = $this->dbo->setQuery($query)->loadObjectList('id', get_class($this->lessonSummary));

        if (version_compare(phpversion(), '5.6.21', 'ge')) {
            /** @var LessonSummary $item */
            foreach ($summary as $item) {
                $item->setProperties($item->toArray());
            }
        }

        return $summary;
    }
}

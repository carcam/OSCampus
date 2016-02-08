<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

namespace Oscampus;

use Oscampus\Lesson\Type\Quiz;
use OscampusFactory;

defined('_JEXEC') or die();

/**
 * Class Certificate
 *
 * @package Oscampus
 */
class Certificate extends AbstractBase
{
    /**
     * Award a certificate if all requirements have been passed
     *
     * @param int          $courseId
     * @param UserActivity $activity
     */
    public function award($courseId, UserActivity $activity)
    {
        if ($courseId) {
            $summary = $activity->summary($courseId);
            if ($summary->viewed == $summary->lessons) {
                $lessons = $activity->getCourse($courseId);
                foreach ($lessons as $lessonId => $lesson) {
                    if ($lesson->type == 'quiz') {
                        if ($lesson->score < Quiz::PASSING_SCORE) {
                            return;
                        }
                    }
                }

                // All requirements passed, award certificate
                $certificate = (object)array(
                    'users_id'    => $summary->users_id,
                    'courses_id'  => $courseId,
                    'date_earned' => OscampusFactory::getDate()->toSql()
                );
                $this->dbo->insertObject('#__oscampus_certificates', $certificate);
            }
        }
    }

    /**
     * Creates a snapshot of the selected course
     *
     * @param int $courseId
     *
     * @return null|object
     */
    public function snapshot($courseId)
    {
        $query = $this->dbo->getQuery(true)
            ->select(
                array(
                    'course.id',
                    'course.difficulty',
                    'course.length',
                    'course.title',
                    'course.released'
                )
            )
            ->from('#__oscampus_courses AS course')
            ->where('course.id = ' . (int)$courseId);

        if ($course = $this->dbo->setQuery($query)->loadObject()) {
            $query = $this->dbo->getQuery(true)
                ->select(
                    array(
                        'lesson.id',
                        'module.title AS module_title',
                        'lesson.title',
                        'lesson.type'
                    )
                )
                ->from('#__oscampus_lessons AS lesson')
                ->innerJoin('#__oscampus_modules AS module ON module.id = lesson.modules_id')
                ->where('module.courses_id = ' . (int)$courseId)
                ->order('module.ordering ASC, lesson.ordering ASC');

            $course->lessons = $this->dbo->setQuery($query)->loadObjectList('id');

            return $course;
        }

        return null;
    }
}

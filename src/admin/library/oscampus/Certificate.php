<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
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
            $summary = array_pop($activity->getLessonSummary($courseId));
            if ($summary->viewed == $summary->lessons) {
                $lessons = $activity->getCourseLessons($courseId);
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
}

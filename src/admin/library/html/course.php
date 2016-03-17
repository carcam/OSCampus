<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

use Oscampus\Activity\CourseStatus;

defined('_JEXEC') or die();

abstract class OscCourse
{
    /**
     * @param object $course
     * @param string $text
     * @param mixed  $attribs
     * @param bool   $uriOnly
     *
     * @return string
     * @throws Exception
     */
    public static function link($course, $text = null, $attribs = null, $uriOnly = false)
    {
        $query        = OscampusRoute::getInstance()->getQuery('course');
        $query['cid'] = isset($course->courses_id) ? $course->courses_id : $course->id;

        $link = 'index.php?' . http_build_query($query);

        if ($uriOnly) {
            return $link;
        }

        return JHtml::_('link', JRoute::_($link), $text ?: $course->title, $attribs);
    }

    /**
     * Translate a difficulty code to text
     *
     * @param string $value
     *
     * @return string
     */
    public static function difficulty($value)
    {
        $difficulties = JHtml::_('osc.options.difficulties');

        foreach ($difficulties as $difficulty) {
            if (!strcasecmp($difficulty->value, $value)) {
                return $difficulty->text;
            }
        }

        return $value . ': ' . JText::_('COM_OSCAMPUS_UNDEFINED');
    }

    /**
     * Generate the start button for a course
     *
     * @param CourseStatus|object $courseActivity
     *
     * @return string
     */
    public static function startbutton($courseActivity)
    {
        $courseId      = @$courseActivity->id;
        $progress      = @$courseActivity->progress;
        $lastLesson    = @$courseActivity->last_lesson;
        $certificateId = @$courseActivity->certificates_id;

        if ($courseId) {
            if ($progress == 0) {
                $icon    = 'fa-play';
                $text    = JText::_('COM_OSCAMPUS_START_THIS_CLASS');
                $attribs = 'class="osc-btn osc-btn-main"';
            } elseif ($progress == 100 && $certificateId) {
                $icon    = 'fa-repeat';
                $text    = JText::_('COM_OSCAMPUS_WATCH_THIS_CLASS_AGAIN');
                $attribs = 'class="osc-btn osc-btn-active"';
            } else {
                $icon    = 'fa-step-forward';
                $text    = JText::_('COM_OSCAMPUS_CONTINUE_THIS_CLASS');
                $attribs = 'class="osc-btn"';
            }

            $button = sprintf('<i class="fa %s"></i> %s', $icon, $text);

            if ($lastLesson) {
                return JHtml::_('osc.link.lessonid', $courseId, $lastLesson, $button, $attribs);
            }

            return JHtml::_('osc.link.lesson', $courseId, 0, $button, $attribs);
        }

        return '';
    }

}

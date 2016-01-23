<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

use Oscampus\Lesson;
use Oscampus\Lesson\Properties;

defined('_JEXEC') or die();

abstract class OscLesson
{
    /**
     * Return the free indicator for guest users
     *
     * @param object $lesson
     *
     * @return string
     */
    public static function freeflag($lesson)
    {
        $user = JFactory::getUser();
        if ($user->guest) {
            if (in_array($lesson->access, $user->getAuthorisedViewLevels())) {
                return '<span class="free_img"></span>';
            }
        }
        return '';
    }

    /**
     * @param Properties $lesson
     * @param string     $text
     * @param mixed      $attribs
     * @param bool       $uriOnly
     *
     * @return string
     */
    public static function link(Properties $lesson, $text = null, $attribs = null, $uriOnly = false)
    {
        if (!$lesson->id) {
            return '';
        }

        $query = OscampusRoute::getInstance()->getQuery('pathways');

        $query['view'] = 'lesson';
        $query['cid']  = $lesson->courses_id;
        $query['lid']  = $lesson->id;

        if (!empty($lesson->pathways_id)) {
            $query['pid'] = $lesson->pathways_id;
        } elseif ($pid = JFactory::getApplication()->input->getInt('pid')) {
            $query['pid'] = $pid;
        }

        $link = 'index.php?' . http_build_query($query);

        if ($uriOnly) {
            return $link;
        }

        return JHtml::_('link', JRoute::_($link), $text ?: $lesson->title, $attribs);
    }

    /**
     * Translate a lesson type code into text
     *
     * @param string $value
     *
     * @return string
     */
    public static function type($value)
    {
        $types = JHtml::_('osc.options.lessontypes');

        foreach ($types as $type) {
            if (!strcasecmp($type->value, $value)) {
                return $type->text;
            }
        }

        return JText::_('COM_OSCAMPUS_UNDEFINED');
    }

    /**
     * Setup js for lesson navigation handling
     *
     * @param Lesson $lesson
     *
     * @return void
     */
    public static function navigation(Lesson $lesson)
    {
        $lessons = array(
            'previous' => $lesson->previous,
            'current'  => $lesson->current,
            'next'     => $lesson->next
        );

        foreach ($lessons as $properties) {
            unset($properties->content);
            $properties->link = JRoute::_(static::link($properties, null, null, true));
        }

        $lessons = json_encode($lessons);

        JText::script('COM_OSCAMPUS_LESSON_LOADING_NEXT');
        JText::script('COM_OSCAMPUS_LESSON_LOADING_PREVIOUS');
        JText::script('COM_OSCAMPUS_LESSON_LOADING_TITLE');

        JHtml::_('osc.jquery');
        JHtml::_('script', 'com_oscampus/lesson.js', false, true);
        JHtml::_('osc.onready', "$.Oscampus.lesson.navigation({$lessons});");
    }
}

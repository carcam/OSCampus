<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Oscampus\Lesson;
use Oscampus\Request;
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
                return '<span class="osc-free-tag">' . JText::_('COM_OSCAMPUS_LESSON_FREE') . '</span>';
            }
        }
        return '';
    }

    /**
     * @param Properties|Lesson $lesson
     * @param string            $text
     * @param mixed             $attribs
     * @param bool              $uriOnly
     *
     * @return string
     * @throws Exception
     */
    public static function link($lesson, $text = null, $attribs = null, $uriOnly = false)
    {
        $query = static::linkQuery($lesson);

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

        foreach ($lessons as $key => $properties) {
            $options = new Properties($properties);

            unset($options->content);
            $options->link = JRoute::_(static::link($properties, null, null, true));

            $lessons[$key] = $options;
        }

        $lessons = json_encode($lessons);

        JText::script('COM_OSCAMPUS_LESSON_LOADING_NEXT');
        JText::script('COM_OSCAMPUS_LESSON_LOADING_PREVIOUS');
        JText::script('COM_OSCAMPUS_LESSON_LOADING_TITLE');

        // Fixes the fullscreen navigation.
        // Check if we called the lesson from a normal request or ajax request.
        // An ajax request needs to print the JS right in the output, without
        // pass to the document. Otherwise it won't be added to the output.
        if (!Request::isAjax()) {
            JHtml::_('osc.jquery');
            JHtml::_('script', 'com_oscampus/lesson.js', false, true);
        }

        JHtml::_('osc.onready', "$.Oscampus.lesson.navigation({$lessons});", Request::isAjax());
    }

    /**
     * Create the link to retry a quiz
     *
     * @param Properties|Lesson $lesson
     * @param string            $text
     * @param string|array      $attribs
     * @param bool              $uriOnly
     *
     * @return string
     * @throws Exception
     */
    public static function retrylink($lesson, $text, $attribs = null, $uriOnly = false)
    {
        $query         = static::linkQuery($lesson);
        $query['task'] = 'lesson.retry';

        $link = 'index.php?' . http_build_query($query);
        if ($uriOnly) {
            return $link;
        }

        return JHtml::_('link', JRoute::_($link), $text ?: $lesson->title, $attribs);
    }

    /**
     * Get the base url query the lesson
     *
     * @param Properties|Lesson $lesson
     *
     * @return array|null
     * @throws Exception
     */
    protected static function linkQuery($lesson)
    {
        if ($lesson instanceof Lesson) {
            $properties = $lesson->current;
        } elseif ($lesson instanceof Properties) {
            $properties = $lesson;
        } else {
            throw new Exception(
                JText::sprintf(
                    'COM_OSCAMPUS_ERROR_ARGUMENT_INVALID',
                    __CLASS__,
                    __METHOD__,
                    getType($lesson)
                )
            );
        }

        if (!$properties->id) {
            return null;
        }

        $query = OscampusRoute::getInstance()->getQuery('course');

        $query['view'] = 'lesson';
        $query['cid']  = $properties->courses_id;
        $query['lid']  = $properties->id;

        return $query;
    }
}

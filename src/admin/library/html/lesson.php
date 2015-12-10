<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

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
     * @param object $lesson
     * @param string $text
     * @param mixed  $attribs
     * @param bool   $uriOnly
     *
     * @return string
     */
    public static function link($lesson, $text = null, $attribs = null, $uriOnly = false)
    {
        $user = JFactory::getUser();
        if (in_array($lesson->access, $user->getAuthorisedViewLevels())) {
            $query         = OscampusRoute::getInstance()->getQuery('pathways');
            $query['view'] = 'lesson';
            $query['cid']  = $lesson->courses_id;
            $query['idx']  = $lesson->index;

            if (!empty($lesson->pathways_id)) {
                $query['pid'] = $lesson->pathways_id;
            } elseif ($pid = JFactory::getApplication()->input->getInt('pid')) {
                $query['pid'] = $pid;
            }

            $link = 'index.php?' . http_build_query($query);

        } else {
            // @TODO: Determine link for inaccessible lessons
            $link = 'javascript:alert(\'under construction\');';
        }

        if ($uriOnly) {
            return $link;
        }

        return JHtml::_('link', JRoute::_($link), $text ?: $lesson->title, $attribs);
    }

    /**
     * Load supporting js for the lesson ordering panel in the course editing form
     *
     * @param string       $container
     * @param array|string $options
     *
     * @return void
     */
    public static function ordering($container = null, $options = null)
    {
        JHtml::_('osc.jquery');
        JHtml::_('osc.jui');
        JHtml::_('script', 'com_oscampus/lesson.js', false, true);

        if ($options && is_string($options)) {
            $options = json_decode($options, true);
        }
        if (!is_array($options)) {
            $options = array();
        }
        if ($container) {
            $options['container'] = $container;
        }

        $options = json_encode($options);
        JHtml::_('osc.onready', "$.Oscampus.lesson.ordering({$options});");
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
}

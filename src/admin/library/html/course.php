<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

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
}

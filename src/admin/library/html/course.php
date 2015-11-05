<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
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
        $query['cid'] = $course->id;

        if (!empty($course->pathways_id)) {
            $query['pid'] = $course->pathways_id;
        } elseif ($pid = JFactory::getApplication()->input->getInt('pid')) {
            $query['pid'] = $pid;
        } else {
            throw new Exception(JText::sprintf('COM_OSCAMPUS_ERROR_PATHWAY_REQUIRED', __METHOD__), 500);
        }

        $link = 'index.php?' . http_build_query($query);

        if ($uriOnly) {
            return $link;
        }

        return JHtml::_('link', JRoute::_($link), $text ?: $course->title, $attribs);
    }
}

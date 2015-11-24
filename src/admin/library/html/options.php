<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

use Oscampus\Course;

defined('_JEXEC') or die();

abstract class OscOptions
{
    public static function difficulty()
    {
        $options = array(
            JHtml::_(
                'select.option',
                Course::BEGINNER,
                JText::_('COM_OSCAMPUS_COURSE_DIFFICULTY_BEGINNER')
            ),
            JHtml::_(
                'select.option',
                Course::INTERMEDIATE,
                JText::_('COM_OSCAMPUS_COURSE_DIFFICULTY_INTERMEDIATE')
            ),
            JHtml::_(
                'select.option',
                Course::ADVANCED,
                JText::_('COM_OSCAMPUS_COURSE_DIFFICULTY_ADVANCED')
            )
        );

        return $options;
    }
}

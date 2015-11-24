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
    /**
     * Get option list of course difficulty levels
     *
     * @return object[]
     */
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

    /**
     * Get option list of known teachers
     *
     * @return object[]
     */
    public static function teachers()
    {
        $db = OscampusFactory::getDbo();
        $db->escape('');

        $query = $db->getQuery(true)
            ->select(
                array(
                    'teacher.id',
                    'user.username',
                    'user.name'
                )
            )
            ->from('#__oscampus_teachers teacher')
            ->innerJoin('#__users user ON user.id = teacher.users_id')
            ->order('user.username');

        $teachers = array_map(
            function($row) {
                return JHtml::_('select.option', $row->id, sprintf('%s (%s)', $row->username, $row->name));
            },
            $db->setQuery($query)->loadObjectList()
        );

        return $teachers;
    }
}

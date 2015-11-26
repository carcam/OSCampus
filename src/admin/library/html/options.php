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
    protected static $cache = array();

    /**
     * Get option list of course difficulty levels
     *
     * @return object[]
     */
    public static function difficulties()
    {
        if (empty(static::$cache['difficulties'])) {
            static::$cache['difficulties'] = array(
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
        }

        return static::$cache['difficulties'];
    }

    /**
     * Get option list of known teachers
     *
     * @return object[]
     */
    public static function teachers()
    {
        if (empty(static::$cache['teachers'])) {
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

            static::$cache['teachers'] = array_map(
                function ($row) {
                    return JHtml::_('select.option', $row->id, sprintf('%s (%s)', $row->username, $row->name));
                },
                $db->setQuery($query)->loadObjectList()
            );
        }

        return static::$cache['teachers'];
    }

    /**
     * Create options list of available pathways
     *
     * @param bool $coreOnly
     *
     * @return object[]
     */
    public static function pathways($coreOnly = true)
    {
        $key = md5(
            json_encode(
                array(
                    'base' => 'pathways',
                    'core' => $coreOnly
                )
            )
        );

        if (empty(static::$cache[$key])) {
            $db    = OscampusFactory::getDbo();
            $query = $db->getQuery(true)
                ->select('pathway.id, pathway.title, access.title level')
                ->from('#__oscampus_pathways pathway')
                ->innerJoin('#__viewlevels access ON access.id = pathway.access')
                ->order('pathway.title');

            if ($coreOnly) {
                $query->where('IFNULL(pathway.users_id, 0) = 0');
            }

            static::$cache[$key] = array_map(function ($row) {
                return (object)array(
                    'value' => $row->id,
                    'text'  => sprintf('%s (%s)', $row->title, $row->level)
                );
            },
                $db->setQuery($query)->loadObjectList()
            );

        }

        return static::$cache[$key];
    }

    public static function tags()
    {
        if (empty(static::$cache['tags'])) {
            $db    = OscampusFactory::getDbo();
            $query = $db->getQuery(true)
                ->select(
                    array(
                        'tag.id AS ' . $db->quoteName('value'),
                        'tag.title AS ' . $db->quoteName('text')
                    )
                )
                ->from('#__oscampus_tags tag')
                ->order('tag.title ASC');

            static::$cache['tags'] = $db->setQuery($query)->loadObjectList();
        }

        return static::$cache['tags'];
    }
}

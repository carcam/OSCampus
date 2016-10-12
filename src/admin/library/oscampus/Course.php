<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Oscampus;

defined('_JEXEC') or die();

abstract class Course extends AbstractBase
{
    const BEGINNER     = 'beginner';
    const INTERMEDIATE = 'intermediate';
    const ADVANCED     = 'advanced';

    const DEFAULT_IMAGE = 'media/com_oscampus/images/default-course.jpg';

    protected static $filePath = null;

    /**
     * Creates a snapshot of the selected course
     *
     * @param int $courseId
     *
     * @return null|object
     */
    public function snapshot($courseId)
    {
        $query = $this->dbo->getQuery(true)
            ->select(
                array(
                    'course.id',
                    'course.difficulty',
                    'course.length',
                    'course.title',
                    'course.released'
                )
            )
            ->from('#__oscampus_courses AS course')
            ->where('course.id = ' . (int)$courseId);

        if ($course = $this->dbo->setQuery($query)->loadObject()) {
            $query = $this->dbo->getQuery(true)
                ->select(
                    array(
                        'lesson.id',
                        'module.title AS module_title',
                        'lesson.title',
                        'lesson.type'
                    )
                )
                ->from('#__oscampus_lessons AS lesson')
                ->innerJoin('#__oscampus_modules AS module ON module.id = lesson.modules_id')
                ->where('module.courses_id = ' . (int)$courseId)
                ->order('module.ordering ASC, lesson.ordering ASC');

            $course->lessons = $this->dbo->setQuery($query)->loadObjectList('id');

            return $course;
        }

        return null;
    }

    /**
     * get a relative path to uploaded file assets for courses/lessons
     *
     * @param string $fileName
     *
     * @return string
     */
    public static function getFilePath($fileName = null)
    {
        if (static::$filePath === null) {
            $filePath = \JComponentHelper::getParams('com_media')->get('file_path');

            static::$filePath = rtrim($filePath, '\\/') . '/oscampus/files';

            $fullPath = JPATH_SITE . '/' . static::$filePath;
            if (!is_dir($fullPath)) {
                jimport('joomla.filesystem.folder');
                \JFolder::create($fullPath);
            }
        }

        return static::$filePath . ($fileName ? '/' . $fileName : '');
    }
}

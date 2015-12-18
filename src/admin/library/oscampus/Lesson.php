<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

namespace Oscampus;

use Oscampus\Lesson\Properties;
use Oscampus\Lesson\Type\AbstractType;

defined('_JEXEC') or die();

/**
 * Class Lesson
 *
 * @package Oscampus
 *
 * @property-read int        $index
 * @property-read Properties $previous
 * @property-read Properties $current
 * @property-read Properties $next
 * @property-read object[]   $files
 *
 */
class Lesson extends AbstractBase
{
    /**
     * @var int
     */
    protected $index = null;

    /**
     * @var Properties
     */
    protected $previous = null;

    /**
     * @var Properties
     */
    protected $current = null;

    /**
     * @var Properties
     */
    protected $next = null;

    /**
     * @var AbstractType
     */
    protected $renderer = null;

    /**
     * @var object[]
     */
    protected $files = array();

    public function __construct(\JDatabase $dbo, Properties $properties)
    {
        $this->previous = $properties;
        $this->current  = clone $properties;
        $this->next     = clone $properties;

        parent::__construct($dbo);
    }

    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;

        } elseif (property_exists($this->current, $name)) {
            return $this->current->$name;
        }

        return null;
    }

    /**
     * Use a zero-based index number to retrieve a lesson
     *
     * @param int          $index
     * @param int          $courseId
     * @param int          $pathwayId
     * @param AbstractType $renderer
     *
     * @return void
     */
    public function loadByIndex($index, $courseId, $pathwayId, AbstractType $renderer = null)
    {
        $query = $this->getQuery()
            ->where(
                array(
                    'course.id = ' . (int)$courseId,
                    'pathway.id = ' . (int)$pathwayId
                )
            );

        $offset = max(0, $index - 1);
        $limit  = $index ? 3 : 2;
        $data   = $this->dbo->setQuery($query, $offset, $limit)->loadObjectList();

        if (count($data) == 1) {
            // Only one lesson found - no previous or next
            array_unshift($data, null);
            $data[] = null;

        } elseif (count($data) == 2) {
            if ($offset == 0) {
                // No previous lesson
                array_unshift($data, null);

            } else {
                // No next lesson
                $data[] = null;
            }
        }

        $this->setLessons($index, $data, $renderer);
    }

    /**
     * Load lesson using its ID. Note that if the pathway is not
     * specified, the first in pathway order will be selected
     *
     * @param int          $lessonId
     * @param int          $pathwayId
     * @param AbstractType $renderer
     *
     * @return void
     */
    public function loadById($lessonId, $pathwayId = null, AbstractType $renderer = null)
    {
        $query = $this->dbo->getQuery(true)
            ->select('cp.pathways_id, cp.courses_id')
            ->from('#__oscampus_lessons AS lesson')
            ->innerJoin('#__oscampus_modules AS module ON module.id = lesson.modules_id')
            ->innerJoin('#__oscampus_courses AS course ON course.id = module.courses_id')
            ->innerJoin('#__oscampus_courses_pathways AS cp ON cp.courses_id = course.id')
            ->innerJoin('#__oscampus_pathways AS pathway ON pathway.id = cp.pathways_id')
            ->where('lesson.id = ' . (int)$lessonId)
            ->order('cp.ordering ASC');

        $result = $this->dbo->setQuery($query)->loadObject();

        $courseId  = $result->courses_id;
        $pathwayId = (int)$pathwayId ?: $result->pathways_id;

        $query = $this->getQuery()
            ->where(
                array(
                    'course.id = ' . $courseId,
                    'pathway.id = ' . $pathwayId
                )
            );

        $lessons = $this->dbo->setQuery($query)->loadObjectList();
        foreach ($lessons as $index => $lesson) {
            if ($lesson->id == $lessonId) {
                $data = array(
                    ($index > 0) ? $lessons[$index - 1] : null,
                    $lesson,
                    isset($lessons[$index + 1]) ? $lessons[$index + 1] : null
                );

                $this->setLessons($index, $data, $renderer);
                return;
            }
        }
    }

    public function render()
    {
        return $this->renderer->render();
    }

    /**
     * Get the base query for finding lessons
     *
     * @return \JDatabaseQuery
     */
    protected function getQuery()
    {
        $query = $this->dbo->getQuery(true)
            ->select(
                array(
                    'lesson.*',
                    'module.courses_id',
                    'module.title AS module_title',
                    'course.title AS course_title',
                    'cp.pathways_id',
                    'pathway.title AS pathway_title'
                )
            )
            ->from('#__oscampus_lessons AS lesson')
            ->innerJoin('#__oscampus_modules AS module ON module.id = lesson.modules_id')
            ->innerJoin('#__oscampus_courses AS course ON course.id = module.courses_id')
            ->innerJoin('#__oscampus_courses_pathways AS cp ON cp.courses_id = course.id')
            ->innerJoin('#__oscampus_pathways AS pathway ON pathway.id = cp.pathways_id')
            ->order('pathway.ordering, cp.ordering, module.ordering, lesson.ordering');

        return $query;
    }

    /**
     * @param int              $index
     * @param array[]|object[] $data
     * @param AbstractType     $renderer
     */
    protected function setLessons($index, array $data, AbstractType $renderer = null)
    {
        $this->index = $index;

        $this->previous->load($data[0]);
        $this->current->load($data[1]);
        $this->next->load($data[2]);

        if ($renderer) {
            $this->renderer = $renderer;
            return;
        }

        $className = '\\Oscampus\\Lesson\Type\\' . ucfirst(strtolower($this->current->type));
        if (class_exists($className)) {
            $this->renderer = new $className($this);
        }
    }
}

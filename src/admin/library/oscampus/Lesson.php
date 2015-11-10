<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

namespace Oscampus;

use Oscampus\Lesson\Properties;

defined('_JEXEC') or die();

/**
 * Class Lesson
 *
 * @package Oscampus
 *
 * @TODO    : \JDatabase changes to \JDatabaseDriver in J!3
 *
 * These properties are returned from the current Properties object
 *
 * @property-read int      $id
 * @property-read int      $index
 * @property-read int      $pathways_id
 * @property-read int      $courses_id
 * @property-read int      $modules_id
 * @property-read string   $title
 * @property-read string   $alias
 * @property-read string   $type
 * @property-read string   $header
 * @property-read string   $footer
 * @property-read int      $access
 * @property-read int      $published
 * @property-read string   $pathway_title
 * @property-read string   $course_title
 * @property-read string   $module_title
 * @property-read object[] $files
 */
class Lesson extends Object
{
    /**
     * @var mixed
     */
    public $content = null;

    /**
     * @var string[]
     */
    protected $files = null;

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
     * @var \JDatabase|\JDatabaseDriver
     */
    protected $dbo = null;

    /**
     * @param Properties                  $properties
     * @param \JDatabase|\JDatabaseDriver $dbo
     */
    public function __construct(Properties $properties, $dbo = null)
    {
        $this->previous = clone $properties;
        $this->current  = clone $properties;
        $this->next     = clone $properties;

        $this->dbo = $dbo ?: \OscampusFactory::getContainer()->dbo;
    }

    public function __get($name)
    {
        if (in_array($name, array('previous', 'current', 'next'))) {
            return $this->$name;

        }

        $method = 'get' . ucfirst(strtolower($name));
        if (method_exists($this, $method)) {
            return $this->$method();

        } elseif (property_exists($this->current, $name)) {
            return $this->current->$name;
        }

        return null;
    }

    /**
     * Use a one-based index number to retrieve a lesson
     *
     * @param int $pathwayId
     * @param int $courseId
     * @param int $index
     */
    public function load($pathwayId, $courseId, $index)
    {
        $query = $this->getQuery()
            ->where(
                array(
                    'course.id = ' . (int)$courseId,
                    'pathway.id = ' . (int)$pathwayId
                )
            );

        $offset = max(0, $index - 1);
        $limit  = $offset ? 3 : 2;
        $data   = $this->dbo->setQuery($query, $offset, $limit)->loadAssocList();

        if (count($data) == 1) {
            // Only one lesson found - no previous or next
            array_unshift($data, array());
            $data[] = array();

        } elseif (count($data) == 2) {
            if ($offset == 0) {
                // No previous lesson
                array_unshift($data, array());

            } else {
                // No next lesson
                $data[] = array();
            }
        }

        $this->reset();
        $this->setLessons($index, $data[0], $data[1], $data[2]);
    }

    /**
     * Get related files for the currently loaded lesson
     *
     * @return object[]
     */
    public function getFiles()
    {
        if ($this->files === null && $this->current->id > 0) {
            $query = $this->dbo->getQuery(true)
                ->select('f.*')
                ->from('#__oscampus_files f')
                ->innerJoin('#__oscampus_files_lessons fl ON fl.files_id = f.id')
                ->where('fl.lessons_id = ' . $this->current->id);

            $this->files = $this->dbo->setQuery($query)->loadObjectList();
        }

        return $this->files ?: array();
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
                    'module.title module_title',
                    'course.title course_title',
                    'cp.pathways_id',
                    'pathway.title pathway_title'
                )
            )
            ->from('#__oscampus_lessons lesson')
            ->innerJoin('#__oscampus_modules module ON module.id = lesson.modules_id')
            ->innerJoin('#__oscampus_courses course ON course.id = module.courses_id')
            ->innerJoin('#__oscampus_courses_pathways cp ON cp.courses_id = course.id')
            ->innerJoin('#__oscampus_pathways pathway ON pathway.id = cp.pathways_id')
            ->order('module.ordering, lesson.ordering');

        return $query;
    }

    /**
     * Set the previous/next lessons from the passed arrays
     *
     * @param int        $index
     * @param null|array $previous
     * @param null|array $current
     * @param null|array $next
     *
     * @throws \Exception
     */
    protected function setLessons($index, array $previous, array $current, array $next)
    {
        if ($current) {
            $this->setProperties($current);
            $this->current->setProperties($current);
            $this->current->index = $index;

            if ($previous) {
                $this->previous->setProperties($previous);
                $this->previous->index = $index - 1;
            }

            if ($next) {
                $this->next->setProperties($next);
                $this->next->index = $index + 1;
            }
        }
    }

    public function reset()
    {
        parent::reset();
        $this->current->reset();
        $this->previous->reset();
        $this->next->reset();
    }
}

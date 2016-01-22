<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

namespace Oscampus;

use DateTime;
use JDatabase;
use JUser;
use Oscampus\Lesson\Properties;
use OscampusTable;

defined('_JEXEC') or die();

class Course extends AbstractBase
{
    const BEGINNER     = 'beginner';
    const INTERMEDIATE = 'intermediate';
    const ADVANCED     = 'advanced';

    const DEFAULT_IMAGE = 'media/com_oscampus/images/default-course.jpg';

    /**
     * @var int
     */
    public $id = null;

    /**
     * @var int
     */
    public $pathwayId = null;

    /**
     * @var string
     */
    public $pathway = null;

    /**
     * @var string
     */
    public $difficulty = null;

    /**
     * @var string
     */
    public $title = null;

    /**
     * @var string
     */
    public $alias = null;

    /**
     * @var string
     */
    public $image = null;

    /**
     * @var string
     */
    public $introtext = null;

    /**
     * @var string
     */
    public $description = null;

    /**
     * @var int
     */
    public $access = null;

    /**
     * @var int
     */
    public $published = null;

    /**
     * @var DateTime
     */
    public $released = null;

    /**
     * @var Properties[]
     */
    public $lessons = array();

    /**
     * @var JUser
     */
    protected $user = null;

    /**
     * @var Properties
     */
    protected $lessonProperties = null;

    public function __construct(JDatabase $dbo, JUser $user, Properties $lessonProperties)
    {
        parent::__construct($dbo);

        $this->user             = $user;
        $this->lessonProperties = $lessonProperties;
    }

    /**
     * @param int $courseId
     * @param int $pathwayId
     *
     * @return Course
     */
    public function load($courseId, $pathwayId = null)
    {
        $course = OscampusTable::getInstance('Courses');
        $course->load($courseId);

        $properties = $course->getProperties();
        foreach ($properties as $name => $value) {
            if (property_exists($this, $name)) {
                $this->$name = $value;
            }
        }
        $this->image = $this->image ?: static::DEFAULT_IMAGE;

        $this->setPathway($pathwayId);
        $this->setLessons();

        return $this;
    }

    /**
     * @param int $pathwayId
     *
     * @return void
     */
    protected function setPathway($pathwayId = null)
    {
        $this->pathwayId = null;
        $this->pathway   = null;

        if ($this->id) {
            $access = join(',', array_unique($this->user->getAuthorisedViewLevels()));

            if ($pathwayId) {
                $where = 'pathway.id = ' . (int)$pathwayId;
            } else {
                $where = array(
                    "access IN ({$access})",
                    'pathway.published = 1',
                    'cp.courses_id = ' . $this->id
                );
            }

            $query = $this->dbo->getQuery(true)
                ->select('pathway.id, pathway.title')
                ->from('#__oscampus_pathways AS pathway')
                ->innerJoin('#__oscampus_courses_pathways AS cp ON cp.pathways_id = pathway.id')
                ->where($where);
            if ($pathway = $this->dbo->setQuery($query)->loadObject()) {
                $this->pathwayId = $pathway->id;
                $this->pathway   = $pathway->title;
            }
        }
    }

    /**
     * Load up the lessons for the current course
     *
     * @return void
     */
    protected function setLessons()
    {
        $query = $this->dbo->getQuery(true)
            ->select('lesson.*, module.courses_id, cp.pathways_id')
            ->from('#__oscampus_lessons AS lesson')
            ->innerJoin('#__oscampus_modules AS module ON module.id = lesson.modules_id')
            ->innerJoin('#__oscampus_courses AS course ON course.id = module.courses_id')
            ->leftJoin('#__oscampus_courses_pathways AS cp ON cp.courses_id = module.courses_id AND cp.pathways_id = ' . (int)$this->pathwayId)
            ->where('module.courses_id = ' . (int)$this->id)
            ->order('module.ordering ASC, lesson.ordering ASC');

        $rows = $this->dbo->setQuery($query)->loadObjectList();

        $this->lessons = array();
        foreach ($rows as $row) {
            $properties              = clone $this->lessonProperties;
            $this->lessons[$row->id] = $properties->load($row);
        }
    }

    /**
     * Creates a snapshot of the current course
     *
     * @param null $courseId
     *
     * @return null|object
     */
    public function snapshot($courseId = null)
    {
        if ($courseId = $courseId ?: $this->id) {
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
        }

        return null;
    }
}

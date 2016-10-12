<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Oscampus;

use Exception;
use JDatabaseDriver;
use JForm;
use Joomla\Registry\Registry as Registry;
use JText;
use JUser;
use Oscampus\Lesson\Properties;
use Oscampus\Lesson\Type\AbstractType;

defined('_JEXEC') or die();

/**
 * Class Lesson
 *
 * @package Oscampus
 *
 * @property-read int          $index
 * @property-read int          $id
 * @property-read int          $modules_id
 * @property-read int          $courses_id
 * @property-read string       $type
 * @property-read string       $title
 * @property-read string       $alias
 * @property-read mixed        $content
 * @property-read string       $description
 * @property-read int          $access
 * @property-read bool         $published
 * @property-read bool         $authorised
 * @property-read Properties   $previous
 * @property-read Properties   $current
 * @property-read Properties   $next
 * @property-read object[]     $files
 * @property-read AbstractType $renderer
 *
 */
class Lesson extends AbstractBase
{
    const SUBCLASS_BASE = '\\Oscampus\\Lesson\Type\\';

    /**
     * @var string
     */
    public $courseTitle = null;

    /**
     * @var string
     */
    public $moduleTitle = null;

    /**
     * @var Registry
     */
    public $metadata = null;

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

    public function __construct(JDatabaseDriver $dbo, Properties $properties)
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
     * @param AbstractType $renderer
     *
     * @return Lesson
     */
    public function loadByIndex($index, $courseId, AbstractType $renderer = null)
    {
        $query = $this->getQuery()
            ->where('course.id = ' . (int)$courseId);

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

        return $this;
    }

    /**
     * Load lesson using its ID.
     *
     * @param int          $lessonId
     * @param AbstractType $renderer
     *
     * @return Lesson
     * @throws Exception
     */
    public function loadById($lessonId, AbstractType $renderer = null)
    {
        $query = $this->dbo->getQuery(true)
            ->select('course.id')
            ->from('#__oscampus_lessons AS lesson')
            ->innerJoin('#__oscampus_modules AS module ON module.id = lesson.modules_id')
            ->innerJoin('#__oscampus_courses AS course ON course.id = module.courses_id')
            ->where(
                array(
                    'lesson.id = ' . (int)$lessonId,
                    'lesson.published = 1',
                    'course.published = 1'
                )
            );

        if ($courseId = $this->dbo->setQuery($query)->loadResult()) {
            $query = $this->getQuery()->where('course.id = ' . $courseId);

            $lessons = $this->dbo->setQuery($query)->loadObjectList();
            foreach ($lessons as $index => $lesson) {
                if ($lesson->id == $lessonId) {
                    $data = array(
                        ($index > 0) ? $lessons[$index - 1] : null,
                        $lesson,
                        isset($lessons[$index + 1]) ? $lessons[$index + 1] : null
                    );

                    $this->setLessons($index, $data, $renderer);
                }
            }

            return $this;
        }

        throw new Exception(JText::_('COM_OSCAMPUS_ERROR_COURSE_NOT_FOUND'), 404);
    }

    /**
     * Wrapper to pass request to properties class.
     *
     * See: \Oscampus\Lesson\Properties::isAuthorised()
     *
     * @param JUser $user
     *
     * @return bool
     */
    public function isAuthorised(JUser $user = null)
    {
        return $this->current->isAuthorised($user);
    }

    /**
     * The primary rendering function for lesson content
     *
     * @return string
     */
    public function render()
    {
        return $this->renderer->render();
    }

    /**
     * Get a thumbnail icon for the lesson
     *
     * @param null $width
     * @param null $height
     *
     * @return string
     */
    public function getThumbnail($width = null, $height = null)
    {
        return $this->renderer->getThumbnail($width, $height);
    }

    public function loadAdminForm(JForm $form, Registry $data)
    {
        $renderer = $this->getRenderer($data->get('type'));
        if ($renderer) {
            $xml = $renderer->prepareAdminData($data);
        } else {
            $xml = null;
        }

        if ($xml && $subForm = $xml->xpath('form')) {
            $form->load($subForm[0]);
        }
    }

    /**
     * Opportunity for Lesson Types to verify and massage content data
     * as needed
     *
     * @param Registry $data
     *
     * @return void
     * @throws Exception
     */
    public function saveAdminChanges(Registry $data)
    {
        $renderer = $this->getRenderer($data->get('type'));
        if ($renderer) {
            $renderer->saveAdminChanges($data);
        }
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
                    'course.title AS course_title'
                )
            )
            ->from('#__oscampus_lessons AS lesson')
            ->innerJoin('#__oscampus_modules AS module ON module.id = lesson.modules_id')
            ->innerJoin('#__oscampus_courses AS course ON course.id = module.courses_id')
            ->where(
                array(
                    'lesson.published = 1',
                    'course.published = 1'
                )
            )
            ->order('module.ordering, lesson.ordering');

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

        $currentValues     = (object)$data[1];
        $this->courseTitle = $currentValues->course_title;
        $this->moduleTitle = $currentValues->module_title;
        $this->metadata    = new Registry($currentValues->metadata);

        $this->previous->load($data[0]);
        $this->current->load($data[1]);
        $this->next->load($data[2]);

        $this->renderer = $renderer ?: $this->getRenderer();
    }

    /**
     * @param string $type
     *
     * @return AbstractType|null
     */
    protected function getRenderer($type = null)
    {
        $type = ucfirst(strtolower($type ?: $this->current->type));
        if (!$type) {
            $type = 'DefaultType';
        }
        $className = static::SUBCLASS_BASE . ucfirst($type);

        if (class_exists($className)) {
            return new $className($this);
        }

        return null;
    }
}

<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

JLoader::import('courselist', __DIR__);

class OscampusModelSearch extends OscampusModelSiteList
{
    /**
     * @var object[]
     */
    protected $pathway = null;

    /**
     * @var object[]
     */
    protected $course = null;

    /**
     * @var object[]
     */
    protected $lesson = null;

    /**
     * @var int
     */
    protected $total = null;

    public function __construct(array $config)
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'types',
                'text',
                'tag',
                'teacher',
                'difficulty',
                'progress'
            );
        }

        parent::__construct($config);
    }

    /**
     * @return object[]
     */
    public function getItems()
    {
        $start = (int)$this->getState('list.start', 0);
        $limit = (int)$this->getState('list.limit');

        $fullList = array_merge(
            $this->tagSection($this->getPathways(), 'pathway'),
            $this->tagSection($this->getCourses(), 'course'),
            $this->tagSection($this->getLessons(), 'lesson')
        );

        $chunks = array_chunk($fullList, $limit);

        $index = intval($start / $limit);
        if (isset($chunks[$index])) {
            return $chunks[$index];
        }

        return array();
    }

    protected function tagSection(array $items, $tag)
    {
        foreach ($items as $item) {
            $item->section = $tag;
        }

        return $items;
    }

    /**
     * @return object[]
     */
    protected function getCourses()
    {
        if ($this->course === null) {
            $this->course = array();

            $types = $this->getState('filter.types');

            if (!$types || strpos($types, 'C') !== false) {
                $model = OscampusModel::getInstance('Courselist');
                $model->getState();

                $this->setModelState($model, 'course.released', 'DESC');

                $this->course = $model->getItems();
            }
        }

        return $this->course;
    }

    /**
     * @return object[]
     */
    protected function getPathways()
    {
        if ($this->pathway === null) {
            $this->pathway = array();

            $types = $this->getState('filter.types');

            if (!$types || strpos($types, 'P') !== false) {
                /** @var OscampusModelPathways $model */
                $model = OscampusModel::getInstance('Pathways');

                $this->setModelState($model, 'IFNULL(pathway.modified, pathway.created)', 'DESC');
                $this->pathway = $model->getItems();
            }
        }

        return $this->pathway;
    }

    /**
     * @return object[]
     */
    protected function getLessons()
    {
        if ($this->lesson === null) {
            $this->lesson = array();

            $types = $this->getState('filter.types');
            if (!$types || strpos($types, 'L') !== false) {
                $db = $this->getDbo();

                $query = $db->getQuery(true)
                    ->select('lesson.id')
                    ->from('#__oscampus_lessons AS lesson')
                    ->innerJoin('#__oscampus_modules AS module ON module.id = lesson.modules_id')
                    ->innerJoin('#__oscampus_courses AS course ON course.id = module.courses_id')
                    ->innerJoin('#__oscampus_courses_pathways AS cp ON cp.courses_id = course.id')
                    ->innerJoin('#__oscampus_pathways AS pathway ON pathway.id = cp.pathways_id')
                    ->where(
                        array(
                            'lesson.type = ' . $db->quote('wistia'),
                            'lesson.published = 1',
                            'course.published = 1',
                            $this->whereAccess('course.access'),
                            'pathway.published = 1',
                            $this->whereAccess('pathway.access')
                        )
                    )
                    ->group('lesson.id');

                if ($text = $this->getState('filter.text')) {
                    $query->where($this->whereTextSearch($text, array('lesson.title', 'lesson.description')));
                }

                if ($tagId = (int)$this->getState('filter.tag')) {
                    $query
                        ->leftJoin('#__oscampus_courses_tags AS ct ON ct.courses_id = course.id')
                        ->where('ct.tags_id = ' . $tagId);
                }

                $query->order('IFNULL(lesson.modified, lesson.created) DESC');

                $lessons = $db->setQuery($query)->loadObjectList();

                $this->lesson = $lessons;
            }
        }

        return $this->lesson;
    }

    public function getTotal($section = null)
    {
        if ($this->total === null) {
            $this->getItems();

            $this->total = count($this->course)
                + count($this->pathway)
                + count($this->lesson);
        }

        if ($section) {
            $section = strtolower($section);
            if (property_exists($this, $section)) {
                return count($this->$section);
            }
        }

        return $this->total;
    }

    protected function setModelState(OscampusModelList $model, $ordering = null, $direction = null)
    {
        // Make sure state get initialized
        $model->getState();

        $state = $this->getState()->getProperties();
        foreach ($state as $key => $value) {
            if (strpos($key, 'filter.') === 0) {
                $model->setState($key, $value);
            }
        }

        $model->setState('list.start', 0);
        $model->setState('list.limit', 0);

        if ($ordering) {
            $model->setState('list.ordering', $ordering);
            $model->setState('list.direction', $direction);
        }
    }

    protected function populateState($ordering = null, $direction = null)
    {
        $app = JFactory::getApplication();

        // Display result types
        $types = $this->getUserStateFromRequest($this->context . '.types', 'types', null, 'cmd');
        $this->setState('filter.types', $types);

        // Text search filter
        $minLength = 2;
        $text      = $app->input->getString('text');
        if ($text && strlen($text) < $minLength) {
            $app->enqueueMessage(JText::sprintf('COM_OSCAMPUS_WARNING_SEARCH_MINTEXT', $minLength), 'notice');
            $text = $app->getUserState($this->context . '.filter.text', '');
        } else {
            $text = $this->getUserStateFromRequest($this->context . '.filter.text', 'text', null, 'string');
        }
        $this->setState('filter.text', $text);

        // Tag filter
        $tagId = $this->getUserStateFromRequest($this->context . '.filter.tag', 'tag', null, 'int');
        $this->setState('filter.tag', $tagId);

        // Teacher filter
        $teacherId = $this->getUserStateFromRequest($this->context . '.filter.teacher', 'teacher', null, 'int');
        $this->setState('filter.teacher', $teacherId);

        // Course difficulty filter
        $difficulty = $this->getUserStateFromRequest(
            $this->context . '.filter.difficulty',
            'difficulty',
            null,
            'cmd'
        );
        $this->setState('filter.difficulty', $difficulty);

        // User progress filter
        $progress = $this->getUserStateFromRequest(
            $this->context . '.filter.progress',
            'progress',
            null,
            'cmd'
        );
        $this->setState('filter.progress', $progress);

        parent::populateState($ordering, $direction);
    }
}

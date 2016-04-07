<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

JLoader::import('courselist', __DIR__);

class OscampusModelSearch extends OscampusModelCourselist
{

    public function getItems()
    {
        $results = (object)array(
            'courses'  => $this->getCourses(),
            'pathways' => $this->getPathways()
        );
        return $results;
    }

    public function getCourses()
    {
        $types = (array)$this->getState('show.types');

        if (!$types || in_array('C', $types)) {
            return parent::getItems();
        }

        return array();
    }

    public function getPathways()
    {
        $types = (array)$this->getState('show.types');

        if (!$types || in_array('P', $types)) {
            /** @var OscampusModelPathways $model */
            $model = OscampusModel::getInstance('Pathways');

            $model->setState('filter.text', $this->getState('filter.text'));
            $model->setState('filter.tag', $this->getState('filter.tag'));

            return $model->getItems();
        }

        return array();
    }

    protected function getLessonQuery()
    {

    }

    protected function populateState($ordering = 'course.title', $direction = 'ASC')
    {
        $app = JFactory::getApplication();

        // Display result types
        $types = (array)$this->getUserStateFromRequest($this->context . '.types', 'types', array(), 'array');
        $this->setState('show.types', array_filter($types));

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

        // Ignore pagination for now
        $this->setState('list.start', 0);
        $this->setState('list.limit');
    }
}

<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

namespace Oscampus\Module;

use JDatabase;
use JHtml;
use JModuleHelper;
use JRegistry;
use JText;
use OscampusFactory;
use OscampusModel;
use OscampusModelPathway;

defined('_JEXEC') or die();

class Search
{
    /**
     * @var string
     */
    protected $id = null;

    protected $name = 'mod_oscampus_search';

    /**
     * @var JRegistry
     */
    protected $params = null;

    /**
     * @var OscampusModelPathway
     */
    protected $model = null;

    /**
     * @var JDatabase
     */
    protected $db = null;

    /**
     * @var int
     */
    protected static $instanceCount = 0;

    public function __construct(JRegistry $params)
    {
        $this->params = $params;
        $this->model  = OscampusModel::getInstance('Pathway');
        $this->db     = OscampusFactory::getDbo();

        self::$instanceCount++;
        $this->id = $this->name . '_' . self::$instanceCount;
    }

    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }

        return null;
    }

    /**
     * Get a single input filter by name
     *
     * @param string $name
     *
     * @return string|null
     */
    public function getFilter($name)
    {
        $method = 'createFilter' . ucfirst(strtolower($name));
        if (method_exists($this, $method)) {
            if ($filter = $this->$method()) {
                return $filter;
            }
        }

        return null;
    }

    /**
     * Get an associative array of defined input filters
     *
     * @return string[]
     */
    public function getFilters()
    {
        $methods = get_class_methods($this);
        $filters = array();

        foreach ($methods as $method) {
            if (strpos($method, 'createFilter') === 0) {
                $name = strtolower(str_replace('createFilter', '', $method));
                if ($filter = $this->$method()) {
                    $filters[$name] = $filter;
                }
            }
        }

        return $filters;
    }

    /**
     * pathway filter
     *
     * @return string
     */
    protected function createFilterPathway()
    {
        $user   = OscampusFactory::getUser();
        $levels = join(',', $user->getAuthorisedViewLevels());

        $pathwayQuery = $this->db->getQuery(true)
            ->select(
                array(
                    'pathway.id AS ' . $this->db->quote('value'),
                    'pathway.title AS ' . $this->db->quote('text')
                )
            )
            ->from('#__oscampus_pathways AS pathway')
            ->innerJoin('#__oscampus_courses_pathways AS cp ON cp.pathways_id = pathway.id')
            ->innerJoin('#__oscampus_courses AS course ON course.id = cp.courses_id')
            ->where(
                array(
                    'pathway.users_id = 0',
                    'pathway.published = 1',
                    sprintf('pathway.access IN (%s)', $levels),
                    'course.published = 1',
                    sprintf('course.access IN (%s)', $levels)
                )
            )
            ->group('pathway.id')
            ->order('cp.ordering ASC');

        $pathways = $this->db->setQuery($pathwayQuery)->loadObjectlist();
        array_unshift(
            $pathways,
            JHtml::_('select.option', '', JText::_('COM_OSCAMPUS_OPTION_SELECT_PATHWAY'))
        );

        $html = JHtml::_(
            'select.genericlist',
            $pathways,
            'pid',
            array('list.select' => $this->model->getState('filter.pathway'))
        );

        return $html;
    }

    /**
     * Tag filter
     *
     * @return string
     */
    protected function createFilterTag()
    {
        $user   = OscampusFactory::getUser();
        $levels = join(',', $user->getAuthorisedViewLevels());

        $tagQuery = $this->db->getQuery(true)
            ->select(
                array(
                    'tag.id AS ' . $this->db->quote('value'),
                    'tag.title AS ' . $this->db->quote('text')
                )
            )
            ->from('#__oscampus_tags AS tag')
            ->innerJoin('#__oscampus_courses_tags AS ct ON ct.tags_id = tag.id')
            ->innerJoin('#__oscampus_courses AS course ON course.id = ct.courses_id')
            ->where(
                array(
                    'course.published = 1',
                    sprintf('course.access IN (%s)', $levels)
                )
            )
            ->group('tag.id')
            ->order('tag.title ASC');

        $tags = $this->db->setQuery($tagQuery)->loadObjectlist();
        array_unshift(
            $tags,
            JHtml::_('select.option', '', JText::_('COM_OSCAMPUS_OPTION_SELECT_TAG'))
        );

        $html = JHtml::_(
            'select.genericlist',
            $tags,
            'tag',
            array('list.select' => $this->model->getState('filter.tag'))
        );

        return $html;
    }

    /**
     * Difficulty Filter
     *
     * @return string
     */
    protected function createFilterDifficulty()
    {
        $difficulty = JHtml::_('osc.options.difficulties');
        array_unshift(
            $difficulty,
            JHtml::_('select.option', '', JText::_('COM_OSCAMPUS_OPTION_SELECT_DIFFICULTY'))
        );

        $html = JHtml::_(
            'select.genericlist',
            $difficulty,
            'difficulty',
            array(
                'list.select' => $this->model->getState('filter.difficulty')
            )
        );

        return $html;
    }

    /**
     * Completion filter. Only available for logged in users
     *
     * @return string|null
     */
    protected function createFilterCompletion()
    {
        $completion = JHtml::_('osc.options.completion');
        array_unshift(
            $completion,
            JHtml::_('select.option', '', JText::_('COM_OSCAMPUS_OPTION_SELECT_COMPLETION'))
        );

        if (!OscampusFactory::getUser()->guest) {
            $html = JHtml::_(
                'select.genericlist',
                $completion,
                'completion',
                array(
                    'list.select' => $this->model->getState('filter.completion')
                )
            );

            return $html;
        }

        return null;
    }

    /**
     * Teacher filter
     *
     * @return string
     */
    protected function createFilterTeacher()
    {
        $user   = OscampusFactory::getUser();
        $levels = join(',', $user->getAuthorisedViewLevels());

        $teacherQuery = $this->db->getQuery(true)
            ->select(
                array(
                    'teacher.id AS ' . $this->db->quote('value'),
                    'user.name AS ' . $this->db->quote('text')
                )
            )
            ->from('#__users AS user')
            ->innerJoin('#__oscampus_teachers AS teacher ON teacher.users_id = user.id')
            ->innerJoin('#__oscampus_courses AS course ON course.teachers_id = teacher.id')
            ->innerJoin('#__oscampus_courses_pathways AS cp ON cp.courses_id = course.id')
            ->innerJoin('#__oscampus_pathways AS pathway ON pathway.id = cp.pathways_id')
            ->where(
                array(
                    'pathway.users_id = 0',
                    'pathway.published = 1',
                    sprintf('pathway.access IN (%s)', $levels),
                    'course.published = 1',
                    sprintf('course.access IN (%s)', $levels)
                )
            )
            ->group('teacher.id')
            ->order('user.name ASC');

        $teachers = $this->db->setQuery($teacherQuery)->loadObjectlist();
        array_unshift(
            $teachers,
            JHtml::_('select.option', '', JText::_('COM_OSCAMPUS_OPTION_SELECT_TEACHER'))
        );

        $html = JHtml::_(
            'select.genericlist',
            $teachers,
            'tid',
            array('list.select' => $this->model->getState('filter.teacher'))
        );

        return $html;
    }

    public function output($layout = null)
    {
        $layout = $layout ?: $this->params->get('layout', 'default');
        require JModuleHelper::getLayoutPath($this->name, $layout);
    }
}

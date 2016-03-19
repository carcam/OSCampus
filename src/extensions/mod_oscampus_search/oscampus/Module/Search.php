<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

namespace Oscampus\Module;

use JDatabaseDriver;
use JHtml;
use JModuleHelper;
use JRegistry as Registry;
use JText;
use JUser;
use OscampusFactory;
use OscampusHelperSite;
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
     * @var Registry
     */
    protected $params = null;

    /**
     * @var OscampusModelPathway
     */
    protected $model = null;

    /**
     * @var JDatabaseDriver
     */
    protected $db = null;

    /**
     * @var int
     */
    protected static $instanceCount = 0;

    public function __construct(Registry $params)
    {
        $this->params = $params;
        $this->model  = OscampusModel::getInstance('Pathway');
        $this->db     = OscampusFactory::getDbo();

        self::$instanceCount++;
        $this->id = $this->name . '_' . self::$instanceCount;

        OscampusHelperSite::loadTheme();
    }

    /**
     * Get a single input filter by name
     *
     * @param string $name
     *
     * @return string|null
     */
    protected function getFilter($name)
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
    protected function getFilters()
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
     * Wrapper for the model state
     *
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    protected function getState($name, $default = null)
    {
        return $this->model->getState($name, $default);
    }

    /**
     * pathway filter
     *
     * @return string
     */
    protected function createFilterPathway()
    {
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
                    $this->getWhereAccess('pathway.access'),
                    'course.published = 1',
                    $this->getWhereAccess('course.access'),
                    'course.released <= NOW()'
                )
            )
            ->group('pathway.id')
            ->order('pathway.ordering ASC');

        $pathways = $this->db->setQuery($pathwayQuery)->loadObjectlist();
        array_unshift(
            $pathways,
            JHtml::_('select.option', '', JText::_('COM_OSCAMPUS_OPTION_SELECT_PATHWAY'))
        );

        $selected = $this->model->getState('filter.pathway');
        $class    = $this->getStateClass($selected);

        $html = JHtml::_(
            'select.genericlist',
            $pathways,
            'pid',
            array(
                'list.select' => $selected,
                'list.attr'   => sprintf('class="%s"', $class)
            )
        );

        return $html;
    }

    /**
     * Topic filter. Defined as any pathway containing a course with the chosen tag.
     *
     * @return string
     */
    protected function createFilterTopic()
    {
        $topicQuery = $this->db->getQuery(true)
            ->select(
                array(
                    'tag.id AS ' . $this->db->quoteName('value'),
                    'tag.title AS ' . $this->db->quoteName('text')
                )
            )
            ->from('#__oscampus_pathways AS pathway')
            ->innerJoin('#__oscampus_courses_pathways AS cp ON cp.pathways_id = pathway.id')
            ->innerJoin('#__oscampus_courses AS course ON course.id = cp.courses_id')
            ->innerJoin('#__oscampus_courses_tags AS ct ON ct.courses_id = course.id')
            ->innerJoin('#__oscampus_tags AS tag ON tag.id = ct.tags_id')
            ->where(
                array(
                    'pathway.users_id = 0',
                    'pathway.published = 1',
                    $this->getWhereAccess('pathway.access'),
                    'course.published = 1',
                    $this->getWhereAccess('course.access'),
                    'course.released <= NOW()'
                )
            )
            ->group('tag.id')
            ->order(
                array(
                    'pathway.ordering ASC',
                    'tag.title ASC'
                )
            );

        $topics = $this->db->setQuery($topicQuery)->loadObjectList();
        array_unshift(
            $topics,
            JHtml::_('select.option', '', JText::_('COM_OSCAMPUS_OPTION_SELECT_TOPIC'))
        );

        $selected = $this->model->getState('filter.topic');
        $class    = $this->getStateClass($selected);

        $html = JHtml::_(
            'select.genericlist',
            $topics,
            'filter_topic',
            array(
                'list.select' => $selected,
                'list.attr'   => sprintf('class="%s"', $class)
            )
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
                    $this->getWhereAccess('course.access'),
                    'course.released <= NOW()'
                )
            )
            ->group('tag.id')
            ->order('tag.title ASC');

        $tags = $this->db->setQuery($tagQuery)->loadObjectlist();
        array_unshift(
            $tags,
            JHtml::_('select.option', '', JText::_('COM_OSCAMPUS_OPTION_SELECT_TAG'))
        );

        $selected = $this->model->getState('filter.tag');
        $class    = $this->getStateClass($selected);

        $html = JHtml::_(
            'select.genericlist',
            $tags,
            'filter_tag',
            array(
                'list.select' => $selected,
                'list.attr'   => sprintf('class="%s"', $class)
            )
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

        $selected = $this->model->getState('filter.difficulty');
        $class    = $this->getStateClass($selected);

        $html = JHtml::_(
            'select.genericlist',
            $difficulty,
            'filter_difficulty',
            array(
                'list.select' => $selected,
                'list.attr'   => sprintf('class="%s"', $class)
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
            $selected = $this->model->getState('filter.completion');
            $class    = $this->getStateClass($selected);

            $html = JHtml::_(
                'select.genericlist',
                $completion,
                'filter_completion',
                array(
                    'list.select' => $selected,
                    'list.attr'   => sprintf('class="%s"', $class)
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
                    $this->getWhereAccess('pathway.access'),
                    'course.published = 1',
                    $this->getWhereAccess('course.access'),
                    'course.released <= NOW()'
                )
            )
            ->group('teacher.id')
            ->order('user.name ASC');

        $teachers = $this->db->setQuery($teacherQuery)->loadObjectlist();
        array_unshift(
            $teachers,
            JHtml::_('select.option', '', JText::_('COM_OSCAMPUS_OPTION_SELECT_TEACHER'))
        );

        $selected = $this->model->getState('filter.teacher');
        $class    = $this->getStateClass($selected);

        $html = JHtml::_(
            'select.genericlist',
            $teachers,
            'filter_teacher',
            array(
                'list.select' => $selected,
                'list.attr'   => sprintf('class="%s"', $class)
            )
        );

        return $html;
    }

    /**
     * Add any scripts needed for module operation
     *
     * @return void
     */
    protected function addScript()
    {
        JHtml::_('osc.jquery');
        $js = <<< JSCRIPT
(function($) {
    $(document).ready(function() {
        $('.osc-clear-filters').on('click', function(evt) {
            evt.preventDefault();
            $(this.form)
                .find(':input')
                .not(':button,:hidden')
                .each(function (index, element) {
                    $(element).val(null);
                });
        });
    });
})(jQuery);
JSCRIPT;

        OscampusFactory::getDocument()->addScriptDeclaration($js);
    }

    /**
     * Get the classname for active vs inactive fields
     *
     * @param mixed $state
     *
     * @return string
     */
    public function getStateClass($state)
    {
        return 'osc-formfield-' . ($state == '' ? 'inactive' : 'active');
    }

    public function output($layout = null)
    {
        $this->addScript();

        $layout = $layout ?: $this->params->get('layout', 'default');
        require JModuleHelper::getLayoutPath($this->name, $layout);
    }

    /**
     * Construct a SQL where item for an access level field
     *
     * @param string $field
     * @param JUser  $user
     *
     * @return string
     */
    protected function getWhereAccess($field, JUser $user = null)
    {
        $user = $user ?: OscampusFactory::getUser();

        $viewLevels = array_unique($user->getAuthorisedViewLevels());

        return sprintf($field . ' IN (%s)', join(', ', $viewLevels));
    }
}

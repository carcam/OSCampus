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
        $access = $user->getAuthorisedViewLevels();

        $pathwayQuery = $this->db->getQuery(true)
            ->select(
                array(
                    'id AS ' . $this->db->quote('value'),
                    'title AS ' . $this->db->quote('text')
                )
            )
            ->from('#__oscampus_pathways')
            ->where(
                array(
                    'users_id = 0',
                    'published = 1',
                    sprintf('access IN (%s)', join(',', $access)),
                    'id IN (SELECT pathways_id From #__oscampus_courses_pathways GROUP BY pathways_id)'
                )
            )
            ->order('ordering ASC');

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
        $levels = $user->getAuthorisedViewLevels();

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
                    sprintf('course.access IN (%s)', join(',', $levels))
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

    public function output($layout = null)
    {
        $layout = $layout ?: $this->params->get('layout', 'default');
        require JModuleHelper::getLayoutPath($this->name, $layout);
    }
}

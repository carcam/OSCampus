<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

namespace Oscampus\Module;

use Exception;
use JDatabaseDriver;
use JHtml;
use JModuleHelper;
use Joomla\Registry\Registry as Registry;
use JText;
use OscampusFactory;
use OscampusHelperSite;
use OscampusModel;
use OscampusModelSearch;
use OscampusUtilitiesArray;

defined('_JEXEC') or die();

class Search extends ModuleBase
{
    /**
     * @var OscampusModelSearch
     */
    protected $model = null;

    /**
     * @var bool
     */
    protected static $javascriptLoaded = false;

    public function __construct(Registry $params, $module)
    {
        parent::__construct($params, $module);

        $this->model = OscampusModel::getInstance('Search');
        OscampusHelperSite::loadTheme();
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
     * Get an input filter by name
     *
     * @param string $name
     *
     * @return string|null
     */
    protected function getFilter($name)
    {
        $method = 'getFilter' . ucfirst(strtolower($name));
        if (method_exists($this, $method)) {
            if ($filter = $this->$method()) {
                return $filter;
            }
        }

        return null;
    }

    /**
     * Tag filter
     *
     * @return string
     */
    protected function getFilterTag()
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
                    $this->model->whereAccess('course.access'),
                    'course.released <= NOW()'
                )
            )
            ->group('tag.id')
            ->order('tag.title ASC');

        $tags = $this->db->setQuery($tagQuery)->loadObjectList();
        array_unshift(
            $tags,
            JHtml::_('select.option', '', JText::_('COM_OSCAMPUS_OPTION_SELECT_TAG'))
        );

        $selected = $this->model->getState('filter.tag');
        $class    = $this->getStateClass($selected);

        $html = JHtml::_(
            'select.genericlist',
            $tags,
            'tag',
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
    protected function getFilterDifficulty()
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
            'difficulty',
            array(
                'list.select' => $selected,
                'list.attr'   => sprintf('class="%s"', $class)
            )
        );

        return $html;
    }

    /**
     * User progress filter. Only available for logged in users
     *
     * @return string|null
     */
    protected function getFilterProgress()
    {
        $progress = JHtml::_('osc.options.progress');
        array_unshift(
            $progress,
            JHtml::_('select.option', '', JText::_('COM_OSCAMPUS_OPTION_SELECT_COMPLETION'))
        );

        $selected = $this->model->getState('filter.progress');
        $class    = $this->getStateClass($selected);

        $html = JHtml::_(
            'select.genericlist',
            $progress,
            'progress',
            array(
                'list.select' => $selected,
                'list.attr'   => sprintf('class="%s"', $class)
            )
        );

        return $html;
    }

    /**
     * Teacher filter
     *
     * @return string
     */
    protected function getFilterTeacher()
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
                    $this->model->whereAccess('pathway.access'),
                    'course.published = 1',
                    $this->model->whereAccess('course.access'),
                    'course.released <= NOW()'
                )
            )
            ->group('teacher.id')
            ->order('user.name ASC');

        $teachers = $this->db->setQuery($teacherQuery)->loadObjectList();
        array_unshift(
            $teachers,
            JHtml::_('select.option', '', JText::_('COM_OSCAMPUS_OPTION_SELECT_TEACHER'))
        );

        $selected = $this->model->getState('filter.teacher');
        $class    = $this->getStateClass($selected);

        $html = JHtml::_(
            'select.genericlist',
            $teachers,
            'teacher',
            array(
                'list.select' => $selected,
                'list.attr'   => sprintf('class="%s"', $class)
            )
        );

        return $html;
    }

    protected function getTypes($class = null)
    {
        $show = (array)$this->model->getState('filter.types');

        $types = array(
            'P' => 'MOD_OSCAMPUS_SEARCH_TYPE_PATHWAY',
            'C' => 'MOD_OSCAMPUS_SEARCH_TYPE_COURSE',
            'L' => 'MOD_OSCAMPUS_SEARCH_TYPE_LESSON'
        );

        $html = array();
        foreach ($types as $type => $label) {
            $attribs = array(
                'name' => 'types[]',
                'type' => 'checkbox',
                'value' => $type
            );
            if ($class) {
                $attribs['class'] = $class;
            }
            if (in_array($type, $show)) {
                $attribs['checked'] = 'checked';
            }

            $html[] = '<div><input ' . OscampusUtilitiesArray::toString($attribs) . '/> ';
            $html[] = JText::_($label) . '</div>';
        }
        $html[] = '<input type="hidden" name="types[]" value=""/>';

        return join("\n", $html);
    }

    /**
     * Add any scripts needed for module operation
     *
     * @return void
     */
    public function addScript()
    {
        if (!static::$javascriptLoaded) {
            JHtml::_('osc.jquery');

            $js = <<< JSCRIPT
(function($) {
    $(document).ready(function() {
        var forms = $('form[name=oscampusFilter]');
        forms.find('select, input[type=checkbox]').on('change', function(evt) {
            this.form.submit();
        });
        
        forms.find('input[type=text]').on('keypress', function(evt) {
            if (evt.keyCode === 13) {
                this.form.submit();
            }
        });
        
        $('.osc-clear-filters').on('click', function(evt) {
            evt.preventDefault();
            $(this.form)
                .find(':input')
                .not(':button,:hidden')
                .each(function (index, element) {
                    $(element).val(null);
                });
            
            this.form.submit();
        });
    });
})(jQuery);
JSCRIPT;

            OscampusFactory::getDocument()->addScriptDeclaration($js);

            static::$javascriptLoaded = true;
        }
    }

    /**
     * Get the classname for active vs inactive fields
     *
     * @param mixed $state
     *
     * @return string
     */
    protected function getStateClass($state)
    {
        return 'osc-formfield-' . ($state == '' ? 'inactive' : 'active');
    }
}

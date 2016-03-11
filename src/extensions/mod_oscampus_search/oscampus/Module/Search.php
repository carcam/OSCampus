<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

namespace Oscampus\Module;

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
     * @var int
     */
    protected static $instanceCount = 0;

    public function __construct(JRegistry $params)
    {
        $this->params = $params;
        $this->model = OscampusModel::getInstance('Pathway');

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
     * Returns array of form fields for use in filtering classes
     *
     * @return string[]
     */
     protected function getFilters()
    {
        $db  = OscampusFactory::getDbo();

        $filters = array();

        // Create Pathway/topic selector
        $pathwayQuery = $db->getQuery(true)
            ->select(
                array(
                    'id AS ' . $db->quote('value'),
                    'title AS ' . $db->quote('text')
                )
            )
            ->from('#__oscampus_pathways')
            ->where(
                array(
                    'users_id = 0',
                    'published = 1'
                )
            )
            ->order('ordering ASC');

        $pathways = $db->setQuery($pathwayQuery)->loadObjectlist();
        array_unshift(
            $pathways,
            JHtml::_('select.option', '', JText::_('COM_OSCAMPUS_OPTION_SELECT_PATHWAY'))
        );

        $filters['pathway'] = JHtml::_(
            'select.genericlist',
            $pathways,
            'pid',
            array('list.select' => $this->model->getState('filter.pathway'))
        );

        // Create difficulty selector
        $difficulty = JHtml::_('osc.options.difficulties');
        array_unshift(
            $difficulty,
            JHtml::_('select.option', '', JText::_('COM_OSCAMPUS_OPTION_SELECT_DIFFICULTY'))
        );
        $filters['difficulty'] = JHtml::_(
            'select.genericlist',
            $difficulty,
            'difficulty',
            array(
                'list.select' => $this->model->getState('filter.difficulty')
            )
        );

        // Completion options
        $completion = JHtml::_('osc.options.completion');
        array_unshift(
            $completion,
            JHtml::_('select.option', '', JText::_('COM_OSCAMPUS_OPTION_SELECT_COMPLETION'))
        );

        if (!OscampusFactory::getUser()->guest) {
            $filters['completion'] = JHtml::_(
                'select.genericlist',
                $completion,
                'completion',
                array(
                    'list.select' => $this->model->getState('filter.completion')
                )
            );
        }

        return $filters;
    }

    public function addJS()
    {
        JHtml::_('osc.jquery');
        $js = <<<JSCRIPT
    jQuery(document).ready(function() {
        jQuery('#formFilter select').on('change', function(evt) {
            this.form.submit();
        });
    });
JSCRIPT;

        OscampusFactory::getDocument()->addScriptDeclaration($js);
    }

    public function output($layout = null)
    {
        $layout = $layout ?: $this->params->get('layout', 'default');
        require JModuleHelper::getLayoutPath($this->name, $layout);
    }
}

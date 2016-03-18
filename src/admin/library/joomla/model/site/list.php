<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

use JRegistry as Registry;

defined('_JEXEC') or die();

jimport('joomla.application.component.modellist');

abstract class OscampusModelSiteList extends JModelList
{
    public function getState($property = null, $default = null)
    {
        $init  = !$this->__state_set;
        $state = parent::getState();
        if ($init) {
            $state->set('parameters.component', OscampusComponentHelper::getParams());
        }
        return parent::getState($property, $default);
    }

    /**
     * Get component params merged with menu params
     *
     * @return Registry
     */
    public function getParams()
    {
        $state  = $this->getState();
        $merged = $state->get('parameters.merged', null);
        if ($merged === null) {
            $merged = clone $state->get('parameters.component');
            $menu   = $state->get('parameters.menu');
            $merged->merge($menu);
            $state->set('parameters.merged', $merged);
        }

        return $merged;
    }

    /**
     * Create a where clause of OR conditions for a text search
     * across one or more fields
     *
     * @param string          $text
     * @param string|string[] $fields
     *
     * @return string
     */
    protected function getWhereTextSearch($text, $fields)
    {
        if (!is_array($fields)) {
            $fields = (array)$fields;
        }

        $searchText = $this->getDbo()->quote('%' . $text . '%');

        $ors = array();
        foreach ($fields as $field) {
            $ors[] = $field . ' LIKE ' . $searchText;
        }

        if (count($ors) > 1) {
            return sprintf('(%s)', join(' OR ', $ors));
        }

        return array_pop($ors);
    }
}

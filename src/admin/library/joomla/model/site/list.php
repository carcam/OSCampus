<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

use Joomla\Registry\Registry as Registry;

defined('_JEXEC') or die();

jimport('joomla.application.component.modellist');

abstract class OscampusModelSiteList extends OscampusModelList
{
    /**
     * @param string $property
     * @param mixed  $default
     *
     * @return JObject
     */
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
}

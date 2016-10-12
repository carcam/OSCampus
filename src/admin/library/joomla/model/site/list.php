<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
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

    /**
     * Frontend list models should not use the core populate state method
     * as this will cause all sorts of problems for pagination
     *
     * @param string $ordering
     * @param string $direction
     *
     * @throws Exception
     */
    protected function populateState($ordering = null, $direction = null)
    {
        $app = OscampusFactory::getApplication();

        $ordering = $this->getUserStateFromRequest(
            $this->context . '.list.ordering',
            'ordering',
            $ordering,
            'cmd',
            false
        );
        $this->setState('list.ordering', $ordering);

        $direction = $this->getUserStateFromRequest(
            $this->context . '.list.direction',
            'direction',
            $direction,
            'cmd',
            false
        );
        $this->setState('list.direction', $direction);

        $this->setState('list.start', $app->input->get('limitstart', 0, 'uint'));

        $limit = $app->getUserStateFromRequest(
            $this->context . '.list.limit',
            'limit',
            $app->get('list_limit'),
            'uint'
        );
        $this->setState('list.limit', $limit);
    }
}

<?php
/**
 * @package   Oscampus
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2015-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

abstract class OscampusModelSite extends JModelLegacy
{
    /**
     * @var string
     */
    protected $context = null;

    public function __construct($config = array())
    {
        parent::__construct($config);

        $this->context = $this->option . '.' . $this->name;
    }

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
     * @return JRegistry
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

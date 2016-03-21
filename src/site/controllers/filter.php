<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

class OscampusControllerFilter extends OscampusControllerBase
{
    public function courses()
    {
        /** @var OscampusModelSearch $model */

        $app   = OscampusFactory::getApplication();
        $route = OscampusRoute::getInstance();
        $model = OscampusModel::getInstance('Search');
        $model->getState();

        if ($pid = $app->input->getInt('filter_pathway')) {
            // single pathway selected
            $redirect        = $route->getQuery('pathway');
            $redirect['pid'] = $pid;

        } elseif ($topic = $app->input->getInt('filter_topic')) {
            $redirect = $route->getQuery('pathways');

        } else {
            if ($model->activeFilters()) {
                $redirect = $route->getQuery('search');
            }
        }

        if (empty($redirect)) {
            $redirect = $route->getQuery('pathways');
        }

        $this->setRedirect(JRoute::_('index.php?' . http_build_query($redirect)));
    }
}

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
        $app = OscampusFactory::getApplication();

        $route = OscampusRoute::getInstance();

        if ($pid = $app->input->getInt('pid')) {
            // single pathway selected
            $model = OscampusModel::getInstance('Pathway');
            $model->getState();

            $redirect        = $route->getQuery('pathway');
            $redirect['pid'] = $pid;

        } elseif ($topic = $app->input->getInt('filter_topic')) {
            $model = OscampusModel::getInstance('pathways');
            $model->getState();

            $redirect = $route->getQuery('pathways');

        } else {
            $model = OscampusModel::getInstance('Search');

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

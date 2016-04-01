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
        $this->checkToken();

        $app   = OscampusFactory::getApplication();
        $route = OscampusRoute::getInstance();

        if ($topic = $app->input->getInt('filter_topic')) {
            // Topic filter only applies to pathways view
            $model = OscampusModel::getInstance('Pathways');
            $model->getState();

            $redirect = $route->getQuery('pathways');

        } elseif ($pid = $app->input->getInt('filter_pathway')) {
            // single pathway selected just a simple redirect
            $redirect = $route->getQuery('pathway');

            $redirect['pid'] = $pid;

        } else {
            // This will be some sort of course listing
            $model = OscampusModel::getInstance('Search');
            
            if ($model->activeFilters()) {
                $redirect = $route->getQuery('search');
            }
        }

        if (empty($redirect)) {
            $redirect = $route->getQuery('pathways');
        }

        $link = 'index.php?' . http_build_query($redirect);
        echo '<br/>' . JRoute::_($link);
        echo '<pre>';
        print_r($model->getState());

        echo '</pre>';

        //$this->setRedirect(JRoute::_('index.php?' . http_build_query($redirect)));
    }
}

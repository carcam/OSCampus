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

        /**
         * @var OscampusModelCourses $model
         */

        if ($pid = $app->input->getInt('pid')) {
            $model = OscampusModel::getInstance('Pathway');
            $model->getState();

            $query = array(
                'option' => 'com_oscampus',
                'view'   => 'pathway',
                'pid'    => $pid
            );

            $redirect = 'index.php?' . http_build_query($query);

        } else {
            $model = OscampusModel::getInstance('Search');

            if ($model->activeFilters()) {
                $redirect = 'index.php?option=com_oscampus&view=search';
            }
        }

        if (empty($redirect)) {
            $redirect = 'index.php?option=com_oscampus&view=pathways';
        }

        $this->setRedirect(JRoute::_($redirect));
    }
}

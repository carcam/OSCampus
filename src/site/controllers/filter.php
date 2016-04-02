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

        $view = $app->input->getCmd('view');
        $model = OscampusModel::getInstance($view);
        $model->getState();

        $this->setRedirect(JUri::getInstance());
    }
}

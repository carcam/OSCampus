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
    public function pathway()
    {
        $app = OscampusFactory::getApplication();

        if ($pid = $app->input->getInt('pid')) {
            $query = array(
                'option' => 'com_oscampus',
                'view'   => 'pathway',
                'pid'    => $pid
            );

            // Here's some nice fudge! We want to register any additional filters before the redirect
            OscampusModel::getInstance('Pathway')->getState();

            $this->setRedirect(JRoute::_('index.php?' . http_build_query($query)));

        } else {
            $this->setRedirect(JRoute::_('index.php?option=com_oscampus&view=pathways'));
        }
    }
}

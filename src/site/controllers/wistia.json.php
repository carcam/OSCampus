<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

class OscampusControllerWistia extends OscampusControllerJson
{
    public function test()
    {
        echo json_encode('testing JSON Controller');
    }

    /**
     * Toggle the autoplay state
     *
     * @return string The return status
     */
    public function toggleAutoPlayState()
    {
        $session = JFactory::getSession();
        $state = !((bool) $session->get('media_autoplay', true));
        $session->set('media_autoplay', $state);

        echo json_encode((bool)$state);
    }

    /**
     * Toggle the focus state
     *
     * @return string The return status
     */
    public function toggleFocusState()
    {
        $session = JFactory::getSession();
        $state = !((bool) $session->get('media_focus', true));
        $session->set('media_focus', $state);

        echo json_encode($state);
    }

    /**
     * Store the volume level on the session
     *
     * @return string The return status
     */
    public function setVolumeLevel()
    {
        $app = JFactory::getApplication();

        $level = $app->input->get('level', 1);

        $session = JFactory::getSession();
        $session->set('media_volume_level', $level);

        echo json_encode((float)$level);
    }
}

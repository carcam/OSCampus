<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

use Oscampus\Wistia\Download;

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
        $state   = !((bool)$session->get('oscampus.video.autoplay', true));
        $session->set('oscampus.video.autoplay', $state);

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
        $state   = !((bool)$session->get('oscampus.video.focus', true));
        $session->set('oscampus.video.focus', $state);

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
        $session->set('oscampus.video.volume', $level);

        echo json_encode((float)$level);
    }

    /**
     * Check if user has exceeded their download limit
     */
    public function downloadLimit()
    {
        $user = JFactory::getUser();

        $result = array(
            'authorised' => true,
            'period'     => null,
            'error'      => null
        );

        // Only usable by authorised users
        if (!$user->authorise('video.download', 'com_oscampus')) {
            $result['authorised'] = false;
            $result['error']      = JText::_('JERROR_ALERTNOAUTHOR');
        }

        $app    = JFactory::getApplication();
        $params = JComponentHelper::getParams('com_oscampus');

        $limitPeriod = (int)$params->get('videos.downloadLimitPeriod', 7);

        $result['period'] = JText::plural('COM_OSCAMPUS_VIDEO_DOWNLOAD_LIMIT_PERIOD', $limitPeriod);

        if (Download::checkUserExceededDownloadLimit($user->id)) {
            $result['authorised'] = false;

            $period = JText::plural('COM_OSCAMPUS_VIDEO_DOWNLOAD_LIMIT_PERIOD', $limitPeriod);
            $result['error'] = JText::sprintf('COM_OSCAMPUS_ERROR_VIDEO_DOWNLOAD_LIMIT', $result['period']);
        }

        echo json_encode($result);
    }
}

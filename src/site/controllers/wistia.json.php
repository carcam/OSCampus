<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Oscampus\Lesson\Type\Wistia\Download;

defined('_JEXEC') or die();

class OscampusControllerWistia extends OscampusControllerJson
{
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
        $level = (float)JFactory::getApplication()->input->get('level', 1);

        JFactory::getSession()->set('oscampus.video.volume', $level);

        echo json_encode($level);
    }

    /**
     * Check if user has exceeded their download limit
     */
    public function downloadLimit()
    {
        $user     = JFactory::getUser();
        $download = new Download();

        $result = array(
            'authorised' => true,
            'period'     => JText::plural('COM_OSCAMPUS_VIDEO_DOWNLOAD_LIMIT_PERIOD', $download->period),
            'error'      => null
        );

        // Only usable by authorised users
        if (!$user->authorise('video.download', 'com_oscampus')) {
            $result['authorised'] = false;
            $result['error']      = JText::_('JERROR_ALERTNOAUTHOR');

        } elseif ($download->userExceededLimit()) {
            $result['authorised'] = false;
            $result['error']      = JText::sprintf('COM_OSCAMPUS_ERROR_VIDEO_DOWNLOAD_LIMIT', $result['period']);
        }

        echo json_encode($result);
    }
}

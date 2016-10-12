<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Oscampus\Lesson\Type\Wistia\ApiData;
use Oscampus\Lesson\Type\Wistia\Download;

defined('_JEXEC') or die();

class OscampusControllerWistia extends OscampusControllerBase
{
    public function download()
    {
        $this->checkToken();

        $user = OscampusFactory::getUser();

        // Only usable by authorised users
        if (!$user->authorise('video.download', 'com_oscampus')) {
            throw new Exception(JText::_('JERROR_ALERTNOAUTHOR', 401));
        }

        // Check for over download limit
        $download = new Download();
        if ($download->userExceededLimit($user)) {
            throw new Exception(JText::sprintf('COM_OSCAMPUS_ERROR_VIDEO_DOWNLOAD_LIMIT', $download->period), 401);
        }

        $app = OscampusFactory::getApplication();
        $id  = $app->input->getAlnum('id', null);
        if (empty($id)) {
            throw new Exception(JText::sprintf('COM_OSCAMPUS_ERROR_MISSING_ARG', 'id'), 500);
        }

        // Load the selected Media resource
        $download->send($id);

        throw new Exception(JText::_('COM_OSCAMPUS_ERROR_WISTIA_DOWNLOAD_FAILED'));
    }
}

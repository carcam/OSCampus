<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

use Oscampus\Wistia\ApiData;
use Oscampus\Wistia\Download;

defined('_JEXEC') or die();

class OscampusControllerWistia extends OscampusControllerBase
{
    public function download()
    {
        $user = JFactory::getUser();

        // Only usable by authorised users
        if (!$user->authorise('video.download', 'com_oscampus')) {
            throw new Exception(JText::_('JERROR_ALERTNOAUTHOR', 401));
        }

        if (!JSession::checkToken()) {
            throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 401);
        }

        $app = JFactory::getApplication();
        $db  = JFactory::getDbo();

        // Look for an ID
        $id = $app->input->getAlnum('id', null);

        if (empty($id)) {
            throw new Exception(JText::_('COM_OSWISTIA_ERROR_NOARGS'), 500);
        }

        $params = JComponentHelper::getParams('com_oscampus');

        if (Download::checkUserExceededDownloadLimit($user->id)) {
            throw new Exception(JText::sprintf('COM_OSWISTIA_ERROR_DOWNLOAD_LIMIT', $downloadLimitPeriod), 401);
        }

        // Load the Data API
        $apikey = $params->get('wistia.apikey');
        if (empty($apikey)) {
            throw new Exception(JText::_('COM_OSWISTIA_ERROR_NOAPIKEY'), 500);
        }
        $wistia = new ApiData($apikey);

        // Load the selected Media resource
        $media = $wistia->selectAsset($id);
        if (empty($media) || empty($media->selected)) {
            throw new Exception(JText::sprintf('COM_OSWISTIA_ERROR_MEDIANOTFOUND', $id), 500);
        }

        Download::log($media, $user);

        list(, $extension) = explode('/', $media->selected->contentType);

        // Manually set response headers since Joomla rendering only gets in the way
        header('Content-Type: ' . $media->selected->contentType);
        header('Content-Disposition: attachment; filename=' . $media->name . '.' . $extension);

        $ch = curl_init($media->selected->url);
        curl_exec($ch);
        curl_close($ch);

        jexit();
    }
}

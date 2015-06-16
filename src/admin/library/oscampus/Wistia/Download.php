<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */
namespace Oscampus\Wistia;

use JComponentHelper;
use JFactory;
use JUser;

defined('_JEXEC') or die();

abstract class Download
{
    /**
     * @param id $userId
     *
     * @return bool
     */
    public static function checkUserExceededDownloadLimit($userId)
    {
        $db     = JFactory::getDbo();
        $params = JComponentHelper::getParams('com_oscampus');

        // Check the download limit
        $downloadLimit       = (int)$params->get('videos.downloadLimit', 20);
        $downloadLimitPeriod = (int)$params->get('videos.downloadLimitPeriod', 7);

        $query = $db->getQuery(true)
            ->select('COUNT(*)')
            ->from('#__oscampus_wistia_download_log')
            ->where("downloaded_by = {$userId}")
            ->where("downloaded BETWEEN TIMESTAMP(DATE_SUB(NOW(), INTERVAL {$downloadLimitPeriod} day)) AND NOW()");

        $total = $db->setQuery($query)->loadResult();

        return $total >= $downloadLimit;
    }

    /**
     * @param object $media
     * @param JUser  $user
     *
     * Add an entry to the download log
     *
     * @return void
     */
    public static function log($media, JUser $user = null)
    {
        $db   = JFactory::getDbo();
        $user = $user ?: JFactory::getUser();

        $insertRow = (object)array(
            'users_id'           => $user->id,
            'downloaded'         => JFactory::getDate()->toSql(),
            'ip'                 => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null,
            'media_hashed_id'    => $media->hashed_id,
            'media_project_name' => $media->project->name,
            'media_name'         => $media->name
        );
        $db->insertObject('#__oscampus_wistia_downloads', $insertRow);
    }
}

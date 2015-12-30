<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */
namespace Oscampus\Lesson\Type\Wistia;

use Exception;
use JDatabase;
use JFactory;
use JRegistry;
use JText;
use JUser;
use OscampusFactory;

defined('_JEXEC') or die();

/**
 * Class Download
 *
 * @package Oscampus\Lesson\Type\Wistia
 *
 * @property-read int $limit
 * @property-read int $period
 */
class Download
{
    /**
     * @var Api
     */
    protected $api = null;

    /**
     * @var int
     */
    protected $limit = null;

    /**
     * @var int
     */
    protected $period = null;

    public function __construct(JRegistry $params = null)
    {
        $params = $params ?: \OscampusComponentHelper::getParams();

        $this->limit  = (int)$params->get('videos.downloadLimit', 20);
        $this->period = (int)$params->get('videos.downloadLimitPeriod', 7);
        $this->api    = new Api($params->get('wistia.apikey'));
    }

    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }

        return null;
    }

    /**
     * @param JUser $user
     *
     * @return bool
     */
    public function userExceededLimit(JUser $user = null)
    {
        $exceeded = true;

        $user = $user ?: OscampusFactory::getUser();
        if ($userId = $user->id) {
            $db = OscampusFactory::getDbo();

            // Check the download limit
            $query = $db->getQuery(true)
                ->select('COUNT(*)')
                ->from('#__oscampus_wistia_downloads')
                ->where(
                    array(
                        'users_id = ' . $userId,
                        "downloaded BETWEEN TIMESTAMP(DATE_SUB(NOW(), INTERVAL {$this->limitPeriod} day)) AND NOW()"
                    )
                );

            $total = $db->setQuery($query)->loadResult();

            $exceeded = ($total >= $this->limit);
        }

        return $exceeded;
    }

    public function send($id, JUser $user = null)
    {
        // Load the selected Media resource
        $video = $this->api->getVideo($id);

        if (!$video->id) {
            throw new Exception(JText::sprintf('COM_OSCAMPUS_ERROR_WISTIA_VIDEO_NOTFOUND', $id), 500);
        }

        $this->log($video, $user);

        $extension = array_pop(explode('/', $video->mimeType));
        header('Content-Type: ' . $video->mimeType);
        header('Content-Disposition: attachment; filename=' . $video->name . '.' . $extension);

        $ch = curl_init($video->url);
        curl_exec($ch);
        curl_close($ch);

        jexit();
    }

    /**
     * @param Video     $video
     * @param JUser     $user
     * @param JDatabase $db
     *
     * Add an entry to the download log
     *
     * @return void
     */
    public function log(Video $video, JUser $user = null, JDatabase $db = null)
    {
        $db   = $db ?: OscampusFactory::getDbo();
        $user = $user ?: OscampusFactory::getUser();

        $insertRow = (object)array(
            'users_id'           => $user->id,
            'downloaded'         => OscampusFactory::getDate()->toSql(),
            'ip'                 => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP) ?: null,
            'media_hashed_id'    => $video->id,
            'media_project_name' => $video->project,
            'media_name'         => $video->name
        );
        $db->insertObject('#__oscampus_wistia_downloads', $insertRow);
    }
}
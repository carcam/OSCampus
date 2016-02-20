<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

namespace Oscampus\Lesson\Type\Wistia;

use Exception;
use JLog;
use JText;

defined('_JEXEC') or die();

/**
 * Class Apidata
 *
 * Main point of communication with the Wistia Data API.
 *
 * See http://wistia.com/doc/data-api
 */
class Api
{
    const BASE_URL  = 'https://api.wistia.com/v1/';
    const USER_NAME = 'api';

    /**
     *
     * @var string
     */
    protected $apiKey = null;

    /**
     *
     * @var array
     */
    protected $cache = array();

    /**
     * @var Video
     */
    protected $video = null;

    /**
     * @param string $apiKey
     *
     * @throws Exception
     */
    public function __construct($apiKey)
    {
        if (empty($apiKey)) {
            throw new Exception(JText::_('COM_OSCAMPUS_WISTIA_ERROR_NOAPIKEY'), 500);
        }
        $this->apiKey = $apiKey;
    }

    /**
     * Fetch all of the projects in this account
     *
     * @return array of stdObjects
     */
    public function projectList()
    {
        if (empty($this->cache['projects'])) {
            $this->cache['projects'] = $this->request('projects');
        }
        return $this->cache['projects'];
    }

    /**
     * Get all base video details
     *
     * @param int $id Wistia identifier for a video can be hashed or not
     *
     * @return object
     */
    protected function getMedia($id = null)
    {
        if (empty($this->cache['media'][$id])) {
            if (!array_key_exists('media', $this->cache)) {
                $this->cache['media'] = array();
            }
            $this->cache['media'][$id] = $this->request('medias/' . $id);
        }
        return $this->cache['media'][$id];
    }

    /**
     * Select a single media asset from what is available for the specified ID.
     *
     * @param string $id        The ID of the media desired
     * @param int    $minWidth  The minimum width for an asset to be selected
     * @param int    $minHeight The minimu height for an asset to be selected
     * @param array  $types     Array of mime types that allow for an asset to be selected
     *
     * @return Video
     */
    public function getVideo($id, $minWidth = 0, $minHeight = 0, $types = array('video/mp4'))
    {
        if ($media = $this->getMedia($id)) {
            // Look for an asset that's within spec
            foreach ($media->assets as $index => $asset) {
                if (in_array($asset->contentType, $types)) {
                    if ($asset->width >= $minWidth && $asset->height >= $minHeight) {
                        $video = new Video($media, $index);
                        return $video;
                    }
                }
            }

        }
        return null;
    }

    /**
     * Gets the Still Image version of the video
     * @TODO: Possibly need a strategy to extract thumbnail from video asset
     *
     * @param string $id
     *
     * @return null|Video
     */
    public function getThumbnail($id)
    {
        if ($media = $this->getMedia($id)) {
            foreach ($media->assets as $index => $asset) {
                if ($asset->type == 'StillImageFile') {
                    $thumb = new Video($media, $index);

                    // Convert to ssl when needed
                    if (!empty($_SERVER['HTTPS'])) {
                        $thumb->url = str_replace(
                            array('http:', 'embed.wistia'),
                            array('https:', 'embed-ssl.wistia'),
                            $thumb->url
                        );
                    }

                    return $thumb;
                }
            }
        }

        return null;
    }

    /**
     * Make request to Wistia
     *
     * @param string $module
     * @param array  $params
     *
     * @return mixed array/stdobject (from json_decode)
     */
    protected function request($module, $params = null)
    {
        $url = self::BASE_URL . $module . '.json';

        // Set our aparams if we have them
        if ($params) {
            $url .= '?' . http_build_query($params);
        }

        $result = $this->send($url, $params);

        $result = json_decode($result);
        return $result;
    }

    /**
     * Execute GET to Wistia API
     *
     * @param string $url
     *
     * @return mixed
     */
    protected function send($url)
    {
        JLog::add('Request: ' . $url, JLog::INFO, 'oscampus');

        $ch = curl_init();
        curl_setopt_array(
            $ch,
            array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL            => $url,
                CURLOPT_USERPWD        => self::USER_NAME . ':' . $this->apiKey
            )
        );

        $result = curl_exec($ch);
        $info   = curl_getinfo($ch);

        curl_close($ch);
        $this->response = $result;

        JLog::add('Response: ' . json_encode($info), JLog::INFO, 'oscampus');

        return $result;
    }
}

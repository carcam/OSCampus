<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */
namespace oscampus\wistia;

defined('_JEXEC') or die();

/**
 * Class Apidata
 *
 * Main point of communication with the Wistia Data API.
 *
 * See http://wistia.com/doc/data-api
 */
class ApiData
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

    public function __construct($apiKey = null)
    {
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
     * Get all video details
     *
     * @param int $id
     *            Wistia identifier for a video can be hashed or not
     *
     * @return stdClass Video
     */
    public function mediaShow($id = null)
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
     * @param       $id
     *              The ID of the media desired
     * @param int   $minwidth
     *              The minimum width for an asset to be selected
     * @param int   $minheight
     *              The minimu height for an asset to be selected
     * @param array $types
     *              Array of mime types that allow for an asset to be selected
     *
     * @return stdClass
     */
    public function selectAsset($id, $minwidth = 0, $minheight = 0, $types = array('video/mp4'))
    {
        if ($media = $this->mediaShow($id)) {
            $media->selected = null;
            // Look for an asset that's within spec
            foreach ($media->assets as $asset) {
                if (in_array($asset->contentType, $types)) {
                    if ($asset->width >= $minwidth && $asset->height >= $minheight) {
                        $media->selected = $asset;
                        $minwidth        = max($asset->width, $minwidth);
                        $minheight       = max($asset->height, $minheight);
                    }
                }
            }

        }
        return $media;
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

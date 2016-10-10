<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

namespace Oscampus\Lesson\Type\Wistia;

use OscampusFactory;

defined('_JEXEC') or die();

class Video
{
    /**
     * @var int
     */
    public $id = null;

    /**
     * @var string
     */
    public $embedCode = null;

    /**
     * @var string
     */
    public $name = null;

    /**
     * @var string
     */
    public $project = null;

    /**
     * @var \DateTime
     */
    public $created = null;

    /**
     * @var \DateTime
     */
    public $updated = null;

    /**
     * @var int
     */
    public $duration = null;

    /**
     * @var string
     */
    public $description = null;

    /**
     * @var string
     */
    public $thumbUrl = null;

    /**
     * @var array
     */
    public $thumbSize = array();

    /**
     * @var string
     */
    public $url = null;

    /**
     * @var array
     */
    public $size = array();

    /**
     * @var int
     */
    public $fileSize = null;

    /**
     * @var string
     */
    public $mimeType = null;

    public function __construct($media, $assetIndex)
    {
        if (isset($media->assets[$assetIndex])) {
            $this->id          = $media->hashed_id;
            $this->embedCode   = $media->embedCode;
            $this->name        = $media->name;
            $this->project     = $media->project->name;
            $this->created     = OscampusFactory::getDate($media->created);
            $this->updated     = OscampusFactory::getDate($media->updated);
            $this->duration    = $media->duration;
            $this->description = $media->description;
            $this->thumbUrl    = $media->thumbnail->url;
            $this->thumbSize   = array($media->thumbnail->width, $media->thumbnail->height);

            $asset          = $media->assets[$assetIndex];
            $this->url      = $asset->url;
            $this->size     = array($asset->width, $asset->height);
            $this->fileSize = $asset->fileSize;
            $this->mimeType = $asset->contentType;
        }
    }
}

<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

use Oscampus\MobileDetect;

defined('_JEXEC') or die();

abstract class OscWistia
{
    public static function player(
        $id,
        $cacheVideoID = false,
        $forceAutoplay = false,
        $width = null,
        $height = null
    ) {
        error_reporting(-1);
        ini_set('display_errors', 1);

        $session = JFactory::getSession();

        $detect      = new MobileDetect();
        $isNotMobile = !$detect->isMobile();

        $config = array(
            'cacheVideoID' => $cacheVideoID,
            'autoplay'     => $session->get('media_autoplay', true) || $forceAutoplay,
            'focus'        => $session->get('media_focus', true) && $isNotMobile,
            'captions'     => true && $isNotMobile,
            'resumable'    => true,
            'volume'       => $session->get('media_volume_level', 1)
        );

        if ($width !== null) {
            $config['width'] = $width;
        }

        if ($height !== null) {
            $config['height'] = $height;
        }
        $app = JFactory::getApplication();

        // Block features for non authorized users
        if (!JFactory::getUser()->authorise('video.control', 'com_oscampus')) {
            $config['autoplay'] = false;
            $config['focus']    = false;
            $config['captions'] = false;
        }

        $content         = '{wistia}' . $id . '{/wistia}';
        $preparedContent = JHtml::_('content.prepare', $content, $config);

        error_reporting(0);
        ini_set('display_errors', 0);

        return $preparedContent;
    }

}

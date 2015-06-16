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
    /**
     * @param string $id
     * @param bool   $cacheVideoID
     * @param bool   $forceAutoplay
     * @param int    $width
     * @param int    $height
     *
     * @return string
     */
    public static function player(
        $id,
        $cacheVideoID = false,
        $forceAutoplay = false,
        $width = null,
        $height = null
    ) {
        $session = JFactory::getSession();

        $detect      = new MobileDetect();
        $isNotMobile = !$detect->isMobile();

        $config = array(
            'cacheVideoID' => $cacheVideoID,
            'autoplay'     => $session->get('oscampus.video.autoplay', true) || $forceAutoplay,
            'focus'        => $session->get('oscampus.video.focus', true) && $isNotMobile,
            'captions'     => true && $isNotMobile,
            'resumable'    => true,
            'volume'       => $session->get('oscampus.video.volume', 1)
        );

        if ($width !== null) {
            $config['width'] = $width;
        }

        if ($height !== null) {
            $config['height'] = $height;
        }

        // Block features for non authorized users
        if (!JFactory::getUser()->authorise('video.control', 'com_oscampus')) {
            $config['autoplay'] = false;
            $config['focus']    = false;
            $config['captions'] = false;
        }

        $content         = '{wistia}' . $id . '{/wistia}';
        $preparedContent = JHtml::_('content.prepare', $content, $config);

        return $preparedContent . static::addExtraControls();
    }

    /**
     * @return string
     */
    protected static function addExtraControls()
    {
        $user       = JFactory::getUser();
        $authorised = $user->authorise('video.control', 'com_oscampus');
        if ($authorised) {
            JHtml::_('script', 'com_oscampus/utilities.js', false, true);
            JHtml::_('script', 'com_oscampus/wistia.js', false, true);

            $detect = new MobileDetect();
            if (!$detect->isMobile()) {
                $authoriseDownload = $user->authorise('video.download', 'com_oscampus');

                $options = array(
                    'download' => array(
                        'authorised' => $authoriseDownload,
                        'formToken'  => JSession::getFormToken()
                    )
                );

                $options = json_encode($options);
                $js      = array(
                    "<script>",
                    "(function($) {",
                    "   wistiaEmbed.ready(function() {",
                    "      $.Oscampus.wistia.addExtraControls({$options});",
                    "      $.Oscampus.wistia.moveNavigationButtons();",
                    "      $.Oscampus.wistia.fixVideoSizeProportion();",
                    "   });",
                    "})(jQuery);",
                    "</script>"
                );
                return join("\n", $js);
            }
        }
        return '';
    }
}

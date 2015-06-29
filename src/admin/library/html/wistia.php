<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

use Alledia\OSWistia\Pro\Embed as WistiaEmbed;
use Alledia\Framework\Factory as AllediaFactory;

defined('_JEXEC') or die();

require_once JPATH_PLUGINS . '/content/oswistia/include.php';


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

        $detect      = new Mobile_Detect();
        $isNotMobile = !$detect->isMobile();

        $oswistia = AllediaFactory::getExtension('OSWistia', 'plugin', 'content');
        $oswistia->loadLibrary();

        $params = clone $oswistia->params;
        $params->set('cacheVideoID', $cacheVideoID);
        $params->set('autoplay', $session->get('oscampus.video.autoplay', true) || $forceAutoplay);
        $params->set('plugin-focus', $session->get('oscampus.video.focus', true) && $isNotMobile);
        $params->set('volume', $session->get('oscampus.video.volume', 1));

        if (! is_null($width)) {
            $params->set('width', $width);
        }

        if (! is_null($height)) {
            $params->set('height', $height);
        }

        // Block features for non authorized users
        if (!JFactory::getUser()->authorise('video.control', 'com_oscampus')) {
            $params->set('autoplay', false);
            $params->set('focus', false);
            $params->set('captions', false);
        }

        $embed = new WistiaEmbed($id, $params);

        return $embed->toString() . static::addExtraControls();
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

            $detect = new Mobile_Detect();
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
                    "      $.Oscampus.wistia.fixVolumeBug();",
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

<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

namespace Oscampus\Lesson\Type;

use Alledia\Framework\Factory as AllediaFactory;
use Alledia\OSWistia\Pro\Embed as WistiaEmbed;
use JHtml;
use JSession;
use Oscampus\Lesson;
use OscampusFactory;

defined('_JEXEC') or die();

class Wistia extends AbstractType
{
    /**
     * @var string
     */
    protected $id = null;

    /**
     * @var bool
     */
    protected $autoplay = false;

    public function __construct(Lesson $lesson)
    {
        parent::__construct($lesson);

        $content = json_decode($lesson->content);

        $this->id       = empty($content->id) ? null : $content->id;
        $this->autoplay = empty($content->autoplay) ? null : (bool)$content->autoplay;
    }

    public function render(
        $cacheVideoID = false,
        $forceAutoplay = false,
        $width = null,
        $height = null
    ) {
        if (!$this->pluginLoaded()) {
            throw new \Exception(\JText::_('COM_OSCAMPUS_ERROR_WISTIA_NOT_INSTALLED'));
        }

        $session = OscampusFactory::getSession();
        $device  = OscampusFactory::getContainer()->device;

        $isNotMobile = !$device->isMobile();

        $oswistia = AllediaFactory::getExtension('OSWistia', 'plugin', 'content');
        $oswistia->loadLibrary();

        $params = clone $oswistia->params;
        $params->set('cacheVideoID', $cacheVideoID);
        $params->set('autoplay', $session->get('oscampus.video.autoplay', true) || $forceAutoplay);
        $params->set('plugin-focus', $session->get('oscampus.video.focus', true) && $isNotMobile);
        $params->set('volume', $session->get('oscampus.video.volume', 1));

        if (!is_null($width)) {
            $params->set('width', $width);
        }

        if (!is_null($height)) {
            $params->set('height', $height);
        }

        // Block features for non authorized users
        if (!OscampusFactory::getUser()->authorise('video.control', 'com_oscampus')) {
            $params->set('autoplay', false);
            $params->set('focus', false);
            $params->set('captions', false);
        }

        $embed = new WistiaEmbed($this->id, $params);

        return $embed->toString() . $this->addExtraControls();
    }

    /**
     * @return string
     */
    protected function addExtraControls()
    {
        $user       = OscampusFactory::getUser();
        $authorised = $user->authorise('video.control', 'com_oscampus');
        if ($authorised) {
            JHtml::_('script', 'com_oscampus/utilities.js', false, true);
            JHtml::_('script', 'com_oscampus/wistia.js', false, true);

            $device = OscampusFactory::getContainer()->device;
            if (!$device->isMobile()) {
                $authoriseDownload = $user->authorise('video.download', 'com_oscampus');

                $options = json_encode(
                    array(
                        'download' => array(
                            'authorised' => $authoriseDownload,
                            'formToken'  => JSession::getFormToken()
                        )
                    )
                );

                $js = <<< JSCRIPT
<script>
    wistiaEmbed.ready(function() {
        jQuery.Oscampus.wistia.init({$options});
    });
</script>
JSCRIPT;
                return $js;
            }
        }
        return '';
    }

    /**
     * This lesson type requires the OSWistia plugin. Ensure that it's loaded.
     *
     * @return bool
     */
    protected function pluginLoaded()
    {
        $loaded = defined('OSWISTIA_PLUGIN_PATH');
        if (!$loaded) {
            $path = JPATH_PLUGINS . '/content/oswistia/include.php';
            if (is_file($path)) {
                require_once $path;
                $loaded = true;
            }
        }

        return $loaded;
    }
}

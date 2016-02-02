<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

namespace Oscampus\Lesson\Type;

use Alledia\Framework\Factory as AllediaFactory;
use Alledia\OSWistia\Pro\Embed as WistiaEmbed;
use JHtml;
use JRegistry;
use JRoute;
use JSession;
use JText;
use Oscampus\Lesson;
use Oscampus\Lesson\ActivityStatus;
use Oscampus\Lesson\Type\Wistia\Api;
use OscampusComponentHelper;
use OscampusFactory;
use OscampusUtilitiesArray;
use SimpleXMLElement;

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
        $this->autoplay = empty($content->autoplay) ? true : (bool)$content->autoplay;
    }

    public function render()
    {
        if (!$this->pluginLoaded()) {
            throw new \Exception(JText::_('COM_OSCAMPUS_ERROR_WISTIA_NOT_INSTALLED'));
        }

        $oswistia = AllediaFactory::getExtension('OSWistia', 'plugin', 'content');
        $oswistia->loadLibrary();

        if (!$this->lesson->isAuthorised()) {
            return $this->renderStatic();
        }

        /** @var JRegistry $params */
        $session = OscampusFactory::getSession();
        $device  = OscampusFactory::getContainer()->device;
        $params  = clone $oswistia->params;

        $volume   = $session->get('oscampus.video.volume', 1);
        $controls = OscampusFactory::getUser()->authorise('video.control', 'com_oscampus');

        $params->set('volume', $volume);
        $params->set('cacheVideoID', $this->id);

        if ($controls) {
            $autoplay = $session->get('oscampus.video.autoplay', $this->autoplay);
            $focus    = $session->get('oscampus.video.focus', true);
            $isMobile = $device->isMobile();

            $params->set('autoplay', $autoplay);
            $params->set('plugin-focus', $focus && !$isMobile);

        } else {
            // Block features for non-authorised users
            $params->set('autoplay', false);
            $params->set('plugin-focus', false);
            $params->set('captions', false);
        }

        $embed = new WistiaEmbed($this->id, $params);

        $output = $embed->toString();
        if ($this->lesson->isAuthorised()) {
            $output .= $this->setControls($controls);
        }
        return $output;
    }

    /**
     * Render a still image version of this video
     *
     * @return string
     */
    protected function renderStatic()
    {
        $params = OscampusComponentHelper::getParams();
        $api    = new Api($params->get('wistia.apikey'));

        $thumb = $api->getThumbnail($this->id);

        $attribs = array(
            'src'    => $thumb->url,
            'width'  => $thumb->size[0],
            'height' => $thumb->size[1]
        );
        return '<img ' . OscampusUtilitiesArray::toString($attribs) . '/>';
    }

    /**
     * @param bool $controls
     *
     * @return string
     */
    protected function setControls($controls)
    {
        $user   = OscampusFactory::getUser();
        $device = OscampusFactory::getContainer()->device;
        $config = OscampusComponentHelper::getParams();

        JHtml::_('script', 'com_oscampus/screenfull.js', false, true);
        JHtml::_('script', 'com_oscampus/utilities.js', false, true);
        JHtml::_('script', 'com_oscampus/wistia.js', false, true);

        $authoriseDownload = $user->authorise('video.download', 'com_oscampus');

        $menuId      = $config->get('signup.upgrade');
        $upgradeUrl = $menuId ? JRoute::_('index.php?Itemid=' . $menuId) : null;

        $options = json_encode(
            array(
                'mobile'      => $device->isMobile(),
                'formToken'   => JSession::getFormToken(),
                'upgradeUrl' => $upgradeUrl,
                'authorised'  => array(
                    'download' => $authoriseDownload,
                    'controls' => $controls
                )
            )
        );

        JText::script('COM_OSCAMPUS_VIDEO_AUTOPLAY');
        JText::script('COM_OSCAMPUS_VIDEO_DOWNLOAD');
        JText::script('COM_OSCAMPUS_VIDEO_DOWNLOAD_UPGRADE');
        JText::script('COM_OSCAMPUS_VIDEO_DOWNLOAD_UPGRADE_LINK');
        JText::script('COM_OSCAMPUS_VIDEO_FOCUS');
        JText::script('COM_OSCAMPUS_VIDEO_RESUME');

        $js = array(
            "<script>",
            "wistiaEmbed.ready(function() {",
            "    jQuery.Oscampus.wistia.init({$options});",
            "});",
            "</script>"
        );
        return join("\n", $js);
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

    /**
     * Prepare an ActivityStatus for recording user progress.
     *
     * @param ActivityStatus $status
     * @param int            $score
     * @param mixed          $data
     *
     * @return void
     */
    public function prepareActivityProgress(ActivityStatus $status, $score, $data)
    {
        if ($status->score < $score) {
            $status->score = $score;
        }

        $now = OscampusFactory::getDate();
        if ($status->score >= 100) {
            $status->score     = 100;
            $status->completed = $now;
        }
        $status->last_visit = $now;

        $status->data = $data;
    }

    /**
     * Prepare data and provide XML for use in lesson admin UI.
     *
     * @param JRegistry $data
     *
     * @return SimpleXMLElement
     */
    public function prepareAdminData(JRegistry $data)
    {
        $content = $data->get('content');
        if ($content && is_string($content)) {
            $data->set('content', json_decode($content, true));
        }

        $path = __DIR__ . '/wistia.xml';

        $xml = simplexml_load_file($path);

        return $xml;
    }
}

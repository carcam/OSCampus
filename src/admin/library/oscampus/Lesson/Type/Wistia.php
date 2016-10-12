<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Oscampus\Lesson\Type;

use Alledia\Framework\Factory as AllediaFactory;
use Alledia\Framework\Joomla\Extension\Licensed;
use Alledia\OSWistia\Pro\Embed as WistiaEmbed;
use Exception;
use JHtml;
use Joomla\Registry\Registry as Registry;
use JSession;
use JText;
use Oscampus\Activity\LessonStatus;
use Oscampus\Lesson;
use Oscampus\Lesson\Type\Wistia\Api;
use OscampusComponentHelper;
use OscampusFactory;
use OscampusHelper;
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

    /**
     * @var Api
     */
    protected $wistiaApi = null;

    /**
     * @var Licensed
     */
    protected $wistiaPlugin = null;

    public function __construct(Lesson $lesson)
    {
        parent::__construct($lesson);

        $content = json_decode($lesson->content);

        $this->id       = empty($content->id) ? null : $content->id;
        $this->autoplay = empty($content->autoplay) ? true : (bool)$content->autoplay;
    }

    public function render()
    {
        try {
            $oswistia = $this->getPlugin();
            if (!$oswistia) {
                throw new Exception(JText::_('COM_OSCAMPUS_ERROR_WISTIA_NOT_INSTALLED'));
            }

            if (!$oswistia->isPro()) {
                throw new Exception(JText::_('COM_OSCAMPUS_ERROR_WISTIA_PRO_REQUIRED'));
            }
            $oswistia->loadLibrary();

            if (!$this->lesson->isAuthorised()) {
                $thumb = $this->getApi()->getThumbnail($this->id);

                $attribs = array(
                    'src'    => $thumb->url,
                    'width'  => $thumb->size[0],
                    'height' => $thumb->size[1]
                );

                return '<img ' . OscampusUtilitiesArray::toString($attribs) . '/>';
            }

            if (!class_exists('\\Alledia\\OSWistia\\Pro\\Embed')) {
                throw new Exception(JText::_('COM_OSCAMPUS_ERROR_WISTIA_EMBED_NOT_FOUND'));
            }

            /** @var Registry $params */
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
            return $embed->toString() . $this->setControls($controls);

        } catch (Exception $e) {
            return '<div class="osc-alert-warning">' . $e->getMessage() . '</div>';
        }
    }

    public function getThumbnail($width = null, $height = null)
    {
        $thumb = $this->getApi()->getThumbnail($this->id, $width, $height);
        return $thumb->url;
    }

    /**
     * Load our API object only once
     *
     * @return Api
     */
    protected function getApi()
    {
        if ($this->wistiaApi === null) {
            $params          = OscampusComponentHelper::getParams();
            $this->wistiaApi = new Api($params->get('wistia.apikey'));
        }

        return $this->wistiaApi;
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

        $signupType  = 'videos.download.' . ($user->guest ? 'new' : 'upgrade');
        $downloadUrl = OscampusHelper::normalizeUrl($config->get($signupType));

        $options = json_encode(
            array(
                'mobile'     => $device->isMobile(),
                'formToken'  => JSession::getFormToken(),
                'upgradeUrl' => $downloadUrl,
                'authorised' => array(
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

        $shortVideoId = substr($this->id, 0, 3);
        $js = <<<JSCRIPT
<script>
window._wq = window._wq || [];
window._wq.push({
    id: '{$shortVideoId}',
    onReady: function() {
        jQuery.Oscampus.wistia.init({$options});
    }
});
</script>
JSCRIPT;
        return $js;
    }

    /**
     * Get the OSWistia plugin
     *
     * @return Licensed
     */
    protected function getPlugin()
    {
        if ($this->wistiaPlugin === null) {
            if (!defined('OSWISTIA_PLUGIN_PATH')) {
                $path = JPATH_PLUGINS . '/content/oswistia/include.php';
                if (is_file($path)) {
                    require_once $path;
                }
            }
            if (class_exists('\\Alledia\\Framework\\Factory')) {
                $this->wistiaPlugin = AllediaFactory::getExtension('OSWistia', 'plugin', 'content');
            }
        }

        return $this->wistiaPlugin;
    }

    /**
     * Prepare an LessonStatus for recording user progress.
     *
     * @param LessonStatus $status
     * @param int          $score
     * @param mixed        $data
     *
     * @return void
     */
    public function prepareActivityProgress(LessonStatus $status, $score = null, $data = null)
    {
        if ($score !== null && $status->score < $score) {
            $status->score = $score;
        }
        // Ensure we never get more than 100%
        $status->score = min(100, $status->score);


        $now = OscampusFactory::getDate();

        $status->last_visit = $now;

        if (!$status->completed) {
            $status->completed = $now;
        }

        $status->data = $data;
    }

    /**
     * Prepare data and provide XML for use in lesson admin UI.
     *
     * @param Registry $data
     *
     * @return SimpleXMLElement
     */
    public function prepareAdminData(Registry $data)
    {
        $content = $data->get('content');
        if ($content && is_string($content)) {
            $data->set('content', json_decode($content, true));
        }

        $path = __DIR__ . '/wistia.xml';

        $xml = simplexml_load_file($path);

        $oswistia = $this->getPlugin();
        if ($oswistia && $oswistia->isPro()) {
            return $xml;
        }

        // Send message about needing OSWistia Pro
        $content = $xml->xpath("//fieldset[@name='content']");
        $content[0]->addAttribute('description', JText::_('COM_OSCAMPUS_WISTIA_PRO_PLUGIN_REQUIRED'));
        $fields = $content[0]->xpath('//field');
        foreach ($fields as $field) {
            unset($field[0]);
        }

        return $xml;
    }

    /**
     * @param Registry $data
     */
    public function saveAdminChanges(Registry $data)
    {
        $content = $data->get('content');
        if (!is_string($content)) {
            $content = json_encode($content);
        }
        $data->set('content', $content);
    }
}

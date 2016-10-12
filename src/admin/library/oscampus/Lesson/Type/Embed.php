<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

namespace Oscampus\Lesson\Type;

use JHtml;
use Joomla\Registry\Registry;
use Oscampus\Activity\LessonStatus;
use Oscampus\Lesson;
use OscampusFactory;
use SimpleXMLElement;

defined('_JEXEC') or die();

class Embed extends AbstractType
{
    /**
     * @var string
     */
    protected $url = null;

    public function __construct(Lesson $lesson)
    {
        parent::__construct($lesson);

        $content = json_decode($lesson->content);

        $this->url = empty($content->url) ? null : $content->url;

    }

    public function render()
    {
        return JHtml::_('content.prepare', $this->url);
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
        $status->score = 100;
        if (!$status->completed) {
            $status->completed = OscampusFactory::getDate();
        }
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

        $path = __DIR__ . '/embed.xml';

        $xml = simplexml_load_file($path);

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

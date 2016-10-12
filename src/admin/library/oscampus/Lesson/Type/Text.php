<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Oscampus\Lesson\Type;

use JHtml;
use Joomla\Registry\Registry as Registry;
use Oscampus\Activity\LessonStatus;
use OscampusFactory;
use SimpleXMLElement;

defined('_JEXEC') or die();

class Text extends AbstractType
{
    public function render()
    {
        return JHtml::_('content.prepare', $this->lesson->content);
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
     * @param Registry $data
     *
     * @return null|SimpleXMLElement
     */
    public function prepareAdminData(Registry $data)
    {
        $path = __DIR__ . '/text.xml';
        if (is_file($path)) {
            $xml = simplexml_load_file($path);
            return $xml;
        }

        return null;
    }
}

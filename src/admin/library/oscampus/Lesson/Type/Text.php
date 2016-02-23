<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

namespace Oscampus\Lesson\Type;

use JHtml;
use JRegistry;
use Oscampus\Lesson\ActivityStatus;
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
     * Prepare an ActivityStatus for recording user progress.
     *
     * @param ActivityStatus $status
     * @param int            $score
     * @param mixed          $data
     *
     * @return void
     */
    public function prepareActivityProgress(ActivityStatus $status, $score = null, $data = null)
    {
        $status->score = 100;
        if (!$status->completed) {
            $status->completed = OscampusFactory::getDate();
        }
    }

    /**
     * @param JRegistry $data
     *
     * @return null|SimpleXMLElement
     */
    public function prepareAdminData(JRegistry $data)
    {
        $path = __DIR__ . '/text.xml';
        if (is_file($path)) {
            $xml = simplexml_load_file($path);
            return $xml;
        }

        return null;
    }
}

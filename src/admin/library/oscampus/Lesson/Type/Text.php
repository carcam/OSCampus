<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

namespace Oscampus\Lesson\Type;

use Oscampus\Lesson\ActivityStatus;
use Oscampus\Lesson\Type\AbstractType;
use OscampusFactory;

defined('_JEXEC') or die();

class Text extends AbstractType
{
    public function render()
    {
        return $this->lesson->content;
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
        $status->score = 100;
        $status->completed = OscampusFactory::getDate();
    }
}

<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

namespace Oscampus\Lesson\Type;

use JRegistry;
use Oscampus\Lesson\ActivityStatus;

defined('_JEXEC') or die();

class DefaultType extends AbstractType
{
    /**
     * Each lesson type must provide the output, loading of
     * js, etc needed for their particular needs
     *
     * @return string
     */
    public function render()
    {
        return null;
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
        // Nothing to do
    }

    public function prepareAdminData(JRegistry $data)
    {
        $path = __DIR__ . '/default_type.xml';
        if (is_file($path)) {
            $xml = simplexml_load_file($path);
            return $xml;
        }

        return null;
    }
}

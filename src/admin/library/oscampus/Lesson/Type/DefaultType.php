<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

namespace Oscampus\Lesson\Type;

use Joomla\Registry\Registry as Registry;
use Oscampus\Activity\LessonStatus;

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
        // Nothing to do
    }

    public function prepareAdminData(Registry $data)
    {
        $path = __DIR__ . '/default_type.xml';
        if (is_file($path)) {
            $xml = simplexml_load_file($path);
            return $xml;
        }

        return null;
    }
}

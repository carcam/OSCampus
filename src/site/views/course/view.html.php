<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

use Oscampus\File;
use Oscampus\Lesson;

defined('_JEXEC') or die();

class OscampusViewCourse extends OscampusViewSite
{
    /**
     * @var object
     */
    protected $course = null;

    /**
     * @var object
     */
    protected $teacher = null;

    /**
     * @var Lesson[]
     */
    protected $lessons = array();

    /**
     * @var File[]
     */
    protected $files = array();

    /**
     * @var object[]
     */
    protected $viewed = array();

    public function display($tpl = null)
    {
        try {
            /** @var OscampusModelCourse $model */
            $model = $this->getModel();

            $this->course = $model->getCourse();
            if ($this->course) {
                $this->teacher = $model->getTeacher();
                $this->lessons = $model->getLessons();
                $this->files   = $model->getFiles();
                $this->viewed  = $model->getViewedLessons();
            }

            $this->setMetadata(
                $this->course->metadata,
                $this->course->title,
                $this->course->introtext ?: $this->course->description
            );

        } catch (Exception $e) {
            if ($e->getCode() == 404) {
                $this->setLayout('notfound');
            } else {
                throw $e;
            }
        }

        parent::display($tpl);
    }
}

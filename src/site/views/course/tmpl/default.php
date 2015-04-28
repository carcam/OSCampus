<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

/**
 * @var OscampusViewCourse $this
 */

JHtml::_('osc.tabs', '.osc-course-tabs div');
?>

<div class="osc-container oscampus-course">

    <div class="page-header">
        <h1><?php echo $this->course->title; ?></h1>
    </div>

    <div class="osc-course-details">
        <div class="osc-section">
            <div class="block4 osc-course-image">
                <?php echo JHtml::_('image', $this->course->image, $this->course->title); ?>
            </div>
            <div class="block8 osc-course-description">
                <div class="osc-course-info">
                    <strong>Teacher:</strong> <?php echo $this->teacher->name; ?>
                    <br/>
                    <strong>Released:</strong> <?php echo JHtml::_('date', $this->course->released, 'F j, Y'); ?>
                    <br/>
                    <strong>Level:</strong>
                    <?php echo JText::_('COM_OSCAMPUS_DIFFICULTY_' . $this->course->difficulty); ?>
                    <br/>
                    <strong>Certificate:</strong> <?php echo JText::_('COM_OSCAMPUS_CERTIFICATE_REQUIREMENT'); ?>
                </div>
            </div>
        </div>
    </div>
    <!-- .osc-section -->

    <div class="osc-section osc-course-tabs">
        <div data-content="#content-content" class="block2">
            <?php echo JText::_('COM_OSCAMPUS_COURSE_TOC'); ?>
        </div>
        <div data-content="#content-description" class="block2 osc-tab-disabled">
            <?php echo JText::_('COM_OSCAMPUS_COURSE_DESCRIPTION'); ?>
        </div>
        <div data-content="#content-files" class="block2 osc-tab-disabled">
            <?php echo JText::_('COM_OSCAMPUS_COURSE_EXERCISE_FILES'); ?>
        </div>
        <?php
        if ($this->teacher->id) :
            ?>
            <div data-content="#content-teacher" class="block2 osc-tab-disabled">
                <?php echo JText::_('COM_OSCAMPUS_COURSE_TEACHER'); ?>
            </div>
        <?php
        endif;
        ?>
        <div data-content="#content-requirements" class="block2 osc-tab-disabled">
            <?php echo JText::_('COM_OSCAMPUS_COURSE_REQUIREMENTS'); ?>
        </div>
    </div>
    <!-- .osc-course-tabs -->

    <?php
    echo $this->loadTemplate('content');
    echo $this->loadTemplate('description');
    echo $this->loadTemplate('files');
    if ($this->teacher->id) {
        echo $this->loadTemplate('teacher');
    }
    echo $this->loadTemplate('requirements');
    ?>
</div>

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
                    <strong>Teacher:</strong> <?php echo $this->instructor->name; ?>
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
            Table of Contents
        </div>
        <div data-content="#content-description" class="block2 osc-tab-disabled">
            Description
        </div>
        <div data-content="#content-trainer" class="block2 osc-tab-disabled">
            Teacher
        </div>
        <div data-content="#content-requirements" class="block2 osc-tab-disabled">
            Requirements
        </div>
    </div>
    <!-- .osc-course-tabs -->

    <div id="content-content" class="osc-course-tabs-content">
        <div class="osc-table">
            <div class="osc-section osc-row-heading">
                <div class="block12"><i class="uk-icon uk-icon-play"></i> Course separator</div>
            </div>
            <div class="osc-section osc-row-one">
                <div class="block10"><a href="#">Lesson name goes here</a></div>
                <div class="block2 osc-check-viewed"><i class="uk-icon uk-icon-check"></i> Viewed</div>
            </div>
            <div class="osc-section osc-row-two">
                <div class="block10"><a href="#">Lesson name goes here</a></div>
                <div class="block2 osc-check-viewed"></div>
            </div>
            <div class="osc-section osc-row-one">
                <div class="block10"><a href="#">Lesson name goes here</a></div>
                <div class="block2 osc-check-viewed"><i class="uk-icon uk-icon-check"></i> Viewed</div>
            </div>
            <div class="osc-section osc-row-two">
                <div class="block10"><a href="#">Lesson name goes here</a></div>
                <div class="block2 osc-check-viewed"></div>
            </div>
            <div class="osc-section osc-row-one">
                <div class="block10"><a href="#">Lesson name goes here</a></div>
                <div class="block2 osc-check-viewed"></div>
            </div>
            <div class="osc-section osc-row-two">
                <div class="block10"><a href="#">Lesson name goes here</a></div>
                <div class="block2 osc-check-viewed"></div>
            </div>
            <div class="osc-section osc-row-one">
                <div class="block10"><a href="#">Lesson name goes here</a></div>
                <div class="block2 osc-check-viewed"><i class="uk-icon uk-icon-check"></i> Viewed</div>
            </div>
            <div class="osc-section osc-row-heading">
                <div class="block12"><i class="uk-icon uk-icon-play"></i> Course separator</div>
            </div>
            <div class="osc-section osc-row-one">
                <div class="block10"><a href="#">Lesson name goes here</a></div>
                <div class="block2 osc-check-viewed"><i class="uk-icon uk-icon-check"></i> Viewed</div>
            </div>
            <div class="osc-section osc-row-two">
                <div class="block10"><a href="#">Lesson name goes here</a></div>
                <div class="block2 osc-check-viewed"></div>
            </div>
            <div class="osc-section osc-row-one">
                <div class="block10"><a href="#">Lesson name goes here</a></div>
                <div class="block2 osc-check-viewed"><i class="uk-icon uk-icon-check"></i> Viewed</div>
            </div>
            <div class="osc-section osc-row-two">
                <div class="block10"><a href="#">Lesson name goes here</a></div>
                <div class="block2 osc-check-viewed"></div>
            </div>
            <div class="osc-section osc-row-one">
                <div class="block10"><a href="#">Lesson name goes here</a></div>
                <div class="block2 osc-check-viewed"></div>
            </div>
            <div class="osc-section osc-row-two">
                <div class="block10"><a href="#">Lesson name goes here</a></div>
                <div class="block2 osc-check-viewed"></div>
            </div>
            <div class="osc-section osc-row-one">
                <div class="block10"><a href="#">Quiz</a></div>
                <div class="block2 osc-check-viewed"></div>
            </div>
        </div>
    </div>
    <!-- #content-lessons -->

    <div id="content-description" class="osc-course-tabs-content" style="display: none">
        <p>Description goes here</p>
    </div>
    <!-- #content-description -->

    <div id="content-trainer" class="osc-course-tabs-content" style="display: none">
        <p>Trainer goes here</p>
    </div>
    <!-- #content-trainer -->

    <div id="content-requirements" class="osc-course-tabs-content" style="display: none">
        <p>Requirements goes here</p>
    </div>
    <!-- #content-requirements -->

</div>

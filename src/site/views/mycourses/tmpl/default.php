<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

/** @var OscampusViewMycourses $this */
?>
<div class="<?php echo $this->getPageClass('osc-container oscampus-pathways'); ?>" id="oscampus">
    <?php if ($heading = $this->getHeading('COM_SIMPLERENEW_HEADING_MYCOURSES')): ?>
        <div class="page-header">
            <h1><?php echo $heading; ?></h1>
        </div>
    <?php endif; ?>

    <div class="osc-table">
        <div class="osc-section osc-row-heading osc-hide-tablet">
            <div class="block6">
                <i class="fa fa-bars"></i> <?php echo JText::_('COM_OSCAMPUS_COURSE_TITLE'); ?>
            </div>
            <div class="block3">
                <i class="fa fa-calendar"></i> <?php echo JText::_('COM_OSCAMPUS_LAST_VISIT_DATE'); ?>
            </div>
            <div class="block3">
                <i class="fa fa-battery-3"></i> <?php echo JText::_('COM_OSCAMPUS_COURSE_PROGRESS'); ?>
            </div>
        </div>

        <?php foreach ($this->items as $item) : ?>
            <div class="osc-section osc-row-one">
                <div class="block6">
                    <?php echo JHtml::_('osc.course.link', $item); ?>
                </div>
                <div class="block3">
                    <?php echo $item->last_lesson->format('Y-m-d'); ?>
                </div>
                <div class="block3">
                    TBD
                </div>
            </div>
        <?php endforeach; ?>

    </div>
</div>

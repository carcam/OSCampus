<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

$pathwayName = empty($this->items[0]) ? '' : $this->items[0]->pathway;
?>

<div class="osc-container oscampus-pathway">

    <div class="page-header">
        <h1><?php echo $pathwayName; ?></h1>
    </div>

    <?php
    foreach ($this->items as $item) :
        $image = JHtml::_('image', $item->image, $item->title);
        $link = '#';
    ?>
    <div class="osc-section osc-course-item">
        <div class="block4 osc-course-image">
            <?php echo JHtml::_('link', $link, $image); ?>
        </div>
        <div class="block8 osc-course-description">
            <h2><?php echo JHtml::_('link', $link, $item->title); ?></h2>
            <?php echo $item->introtext ?: $item->description; ?>
            <div class="osc-course-start">
                <?php echo JHtml::_('link', $link, JText::_('COM_OSCAMPUS_START_THIS_CLASS')); ?>
            </div>
        </div>
    </div>
    <!-- .osc-section -->

    <div class="osc-section osc-course-list">
        <div class="block12">
            <span class="osc-label osc-label-wordpress">
                WordPress
            </span>
            <span class="osc-label osc-label-basic">
                <?php echo JText::_('COM_OSCAMPUS_DIFFICULTY_' . $item->difficulty); ?>
            </span>
            <span class="osc-label osc-label-time">
                <?php echo JText::plural('COM_OSCAMPUS_COURSE_LENGTH_MINUTES', $item->length); ?>
            </span>
            <span class="osc-label osc-label-trainer">
                <?php echo $item->instructor; ?>
            </span>
        </div>
    </div>
    <!-- .osc-section -->
    <?php
    endforeach;
    ?>

</div>

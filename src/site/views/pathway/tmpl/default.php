<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

/** @var OscampusViewPathway $this */
?>

<div class="osc-container oscampus-pathway">

    <div class="page-header">
        <h1><?php echo $this->pathway->title; ?></h1>
    </div>

    <?php
    foreach ($this->items as $item) :
        $image = JHtml::_('image', $item->image, $item->title);
        $link  = JRoute::_(OscampusRoute::get('pathways') . '&view=course&cid=' . $item->id);
    ?>
    <div class="osc-section osc-course-item">
        <div class="block4 osc-course-image">
            <?php echo JHtml::_('link', $link, $image); ?>
        </div>
        <div class="block8 osc-course-description">
            <h2><?php echo JHtml::_('link', $link, $item->title); ?></h2>
            <?php echo $item->introtext ?: $item->description; ?>
            <div class="osc-course-start">
                <?php echo JHtml::_('link', "javascript:alert('Under Construction');", JText::_('COM_OSCAMPUS_START_THIS_CLASS'), 'class="osc-btn"'); ?>
            </div>
        </div>
    </div>
    <!-- .osc-section -->

    <div class="osc-section osc-course-list">
        <div class="block12">
            <span class="osc-label osc-label-category" style="background-image: url(/path/to/small-image-here.png);">
                <?php echo $item->tags; ?>
            </span>
            <span class="osc-label osc-label-<?php echo $item->difficulty; ?>">
                <?php echo JText::_('COM_OSCAMPUS_DIFFICULTY_' . $item->difficulty); ?>
            </span>
            <span class="osc-label osc-label-time">
                <?php echo JText::plural('COM_OSCAMPUS_COURSE_LENGTH_MINUTES', $item->length); ?>
            </span>
            <span class="osc-label osc-label-teacher">
                <?php echo $item->teacher; ?>
            </span>
        </div>
    </div>
    <!-- .osc-section -->
    <?php
    endforeach;
    ?>

</div>

<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

$item  = $this->item;
$link  = JRoute::_(JHtml::_('osc.course.link', $item, null, null, true));
$image = JHtml::_('image', $item->image, $item->title);
?>
<div class="osc-section osc-course-item">
    <div class="block4 osc-course-image">
        <?php echo JHtml::_('link', $link, $image); ?>
    </div>
    <div class="block8 osc-course-description">
        <h2><?php echo JHtml::_('link', $link, $item->title); ?></h2>
        <?php echo $item->introtext ?: $item->description; ?>
    </div>
</div>
<!-- .osc-section -->

<div class="osc-section osc-course-list">
    <div class="block9">
        <?php
        if ($item->tags) :
            ?>
            <span class="osc-label">
                <i class="fa fa-tag"></i>
                <?php echo $item->tags; ?>
            </span>
            <?php
        endif;
        ?>
        <span class="osc-label">
            <i class="fa fa-list"></i>
            <?php echo JText::plural('COM_OSCAMPUS_COURSE_LESSON_COUNT', $item->lesson_count); ?>
        </span>
        <span class="osc-label">
            <i class="fa fa-calendar"></i>
            <?php echo date('F j, Y', strtotime($item->released)); ?>
        </span>
        <span class="osc-label">
            <i class="fa fa-user"></i> <?php echo $item->teacher; ?>
        </span>
    </div>
    <div class="block3 osc-course-start">
        <?php echo JHtml::_('osc.course.startbutton', $item); ?>
    </div>
</div>
<!-- .osc-section -->

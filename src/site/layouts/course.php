<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

/**
 * @var JLayoutFile $this
 * @var object      $displayData
 * @var string      $layoutOutput
 * @var string      $path
 */

$item    = $displayData;
$link    = JRoute::_(JHtml::_('osc.course.link', $item, null, null, true));
$image   = JHtml::_('image', $item->image, $item->title);
$options = $this->getOptions();

if ($options->get('image', true)) :
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
    <?php
endif;
?>
<!-- .osc-section -->

<div class="osc-section osc-course-list">
    <div class="block9">
        <?php
        if ($options->get('tags', true) && $item->tags) :
            ?>
            <span class="osc-label">
                <i class="fa fa-tag"></i>
                <?php echo $item->tags; ?>
            </span>
            <?php
        endif;

        if ($options->get('lessonCount', true)) :
            ?>
            <span class="osc-label">
                <i class="fa fa-list"></i>
                <?php echo JText::plural('COM_OSCAMPUS_COURSE_LESSON_COUNT', $item->lesson_count); ?>
            </span>
            <?php
        endif;

        if ($options->get('length', true)) :
            ?>
            <span class="osc-label">
                <i class="fa fa-clock-o"></i>
                <?php echo JText::plural('COM_OSCAMPUS_COURSE_LENGTH_MINUTES', $item->length); ?>
            </span>
            <?php
        endif;

        if ($options->get('released', false)) :
            ?>
            <span class="osc-label">
                <i class="fa fa-calendar"></i>
                <?php echo date('F j, Y', strtotime($item->released)); ?>
          </span>
            <?php
        endif;

        if ($options->get('teacher', false)) :
            ?>
            <span class="osc-label">
                <i class="fa fa-user"></i>
                <?php echo $item->teacher; ?>
            </span>
            <?php
        endif;

        if ($options->get('difficulty', true)) :
            ?>
            <span class="osc-label">
                <i class="fa fa-signal"></i>
                <?php echo JHtml::_('osc.course.difficulty', $item->difficulty); ?>
            </span>
            <?php
        endif;

        ?>
    </div>
    <div class="block3 osc-course-start">
        <?php echo JHtml::_('osc.course.startbutton', $item); ?>
    </div>
</div>
<!-- .osc-section -->

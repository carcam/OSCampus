<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

/** @var OscampusViewNewcourses $this */
?>
<div class="<?php echo $this->getPageClass('osc-container oscampus-search'); ?>" id="oscampus">
    <?php if ($heading = $this->getHeading('COM_OSCAMPUS_HEADING_SEARCH')): ?>
        <div class="page-header">
            <h1><?php echo $heading; ?></h1>
        </div>
    <?php endif; ?>

    <?php
    if ($this->items) :
        foreach ($this->items as $item) :
            $link = JRoute::_(JHtml::_('osc.course.link', $item, null, null, true));
            $image = JHtml::_('image', $item->image, $item->title);
            ?>
            <div class="osc-section osc-course-item">
                <div class="block4 osc-course-image">
                    <?php echo JHtml::_('link', $link, $image); ?>
                </div>
                <div class="block8 osc-course-description">
                    <h2><?php echo JHtml::_('link', $link, $item->title); ?></h2>
                    <?php echo $item->introtext ?: $item->description; ?>
                    <?php
                    if (!OscampusFactory::getUser()->guest) :
                        ?>
                        <div class="osc-course-start">
                            <?php echo JHtml::_('osc.course.startbutton', $item); ?>
                        </div>
                        <?php
                    endif;
                    ?>
                </div>
            </div>
            <!-- .osc-section -->

            <div class="osc-section osc-course-list">
                <div class="block12">
                    <?php
                    if ($item->tags) :
                        ?>
                        <span class="osc-label"><i class="fa fa-tag"></i>
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
                        <i class="fa fa-clock-o"></i> <?php echo JText::plural('COM_OSCAMPUS_COURSE_LENGTH_MINUTES',
                            $item->length); ?>
                    </span>
                    <span class="osc-label">
                        <i class="fa fa-user"></i> <?php echo $item->teacher; ?>
                    </span>
                </div>
            </div>
            <!-- .osc-section -->
            <?php
        endforeach;

    else:
        ?>
        <div class="osc-alert-notify"><i class="fa fa-info-circle"></i> <?php
            echo JText::_('COM_OSCAMPUS_PATHWAY_NO_COURSES');
            ?></div>
        <?php
    endif;
    ?>
</div>


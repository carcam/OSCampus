<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

/** @var OscampusViewPathway $this */
?>
<div class="<?php echo $this->getPageClass('osc-container oscampus-pathway'); ?>" id="oscampus">
    <div class="osc-section osc-pathway-filters">
        <form
            name="formFilter"
            id="formFilter"
            method="get"
            action="">
            <?php echo join('', $this->filters); ?>
            <input type="hidden" name="task" value="filter.pathway"/>
        </form>
    </div>

    <div class="page-header">
        <h1><?php echo $this->pathway->title; ?></h1>
    </div>

    <?php
    if (!$this->items) :
        ?>
        <div class="osc-section">
            <?php echo JText::_('COM_OSCAMPUS_PATHWAY_NO_COURSES'); ?>
        </div>
        <?php
    else :
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
                        <i class="fa fa-clock-o"></i>
                        <?php echo JText::plural('COM_OSCAMPUS_COURSE_LENGTH_MINUTES', $item->length); ?>
                    </span>
                    <span class="osc-label">
                        <i class="fa fa-user"></i> <?php echo $item->teacher; ?>
                    </span>
                </div>
                <div class="block3 osc-course-start">
                    <?php echo $this->getStartButton($item); ?>
                </div>
            </div>
            <!-- .osc-section -->
            <?php
        endforeach;
    endif;
    ?>
</div>

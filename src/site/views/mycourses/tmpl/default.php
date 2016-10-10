<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

/** @var OscampusViewMycourses $this */
?>
<div class="<?php echo $this->getPageClass('osc-container oscampus-pathways'); ?>" id="oscampus">
    <?php
    if ($heading = $this->getHeading('COM_OSCAMPUS_HEADING_MYCOURSES')):
        ?>
        <div class="page-header">
            <h1><?php echo $heading; ?></h1>
        </div>
        <?php
    endif;
    ?>

    <?php
    if ($this->items) :
        ?>
        <div class="osc-table">
            <div class="osc-section osc-row-heading osc-hide-tablet">
                <div class="block6">
                    <i class="fa fa-bars"></i> <?php echo JText::_('COM_OSCAMPUS_COURSE_TITLE'); ?>
                </div>
                <div class="block3">
                    <i class="fa fa-calendar"></i> <?php echo JText::_('COM_OSCAMPUS_LAST_VISIT_DATE'); ?>
                </div>
                <div class="block3">
                    <i class="fa fa-battery-3"></i> <?php echo JText::_('COM_OSCAMPUS_PROGRESS'); ?>
                </div>
            </div>

            <?php
            foreach ($this->items as $item) :
                $progress = sprintf('%s%%', $item->progress);
                $class = $item->progress == 100 ? 'osc-progress-bar-completed' : '';
                ?>
                <div class="osc-section osc-row-one">
                    <div class="block6">
                        <?php echo JHtml::_('osc.course.link', $item); ?>
                    </div>
                    <div class="block3">
                        <?php echo $item->last_visit->format('F j, Y'); ?>
                    </div>
                    <div class="block3">
                        <span class="osc-progress-bar">
                            <span
                                style="<?php echo sprintf('width: %s;', $progress); ?>"
                                class="<?php echo $class; ?>">
                                <span>
                                    <?php echo ($item->progress >= 30) ? $progress : ''; ?>
                                </span>
                            </span>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
    else :
        ?>
        <div class="osc-section">
            <?php
            $link = JRoute::_(OscampusRoute::getInstance()->get('pathways'));
            $link = JHtml::_('link', $link, JText::_('COM_OSCAMPUS_PATHWAYS_LINK'));
            echo JText::sprintf('COM_OSCAMPUS_MYCOURSES_GET_STARTED', $link);
            ?>
        </div>
        <?php
    endif;
    ?>
</div>

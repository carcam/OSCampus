<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

/**
 * @var OscampusViewLesson $this
 */
$pathwayId = $this->model->getState('pathway.id');
$courseId  = $this->model->getState('course.id');
$previous  = $this->lesson->previous;
$next      = $this->lesson->next;

?>
<div class="osc-btn-group hidden osc-lesson-navigation" id="course-navigation">
    <a href="<?php echo JHtml::_('osc.courselink', $pathwayId, $courseId, null, null, true); ?>" class="osc-btn">
        <i class="fa fa-bars"></i>
        <span class="osc-hide-tablet">
            <?php echo JText::_('COM_OSCAMPUS_HOME'); ?>
        </span>
    </a>

    <?php if ($previous) : ?>
        <a href="<?php echo JHtml::_('osc.lesson.link', $previous, null, null, true); ?>" class="osc-btn" id="prevbut">
            <i class="fa fa-chevron-left"></i>
            <span class="osc-hide-tablet">
                <?php echo JText::_('COM_OSCAMPUS_PREVIOUS'); ?>
            </span>
        </a>
    <?php endif; ?>

    <?php if ($next) : ?>
        <a href="<?php echo JHtml::_('osc.lesson.link', $next, null, null, true); ?>" class="osc-btn" id="nextbut">
            <span class="osc-hide-tablet">
                <?php echo JText::_('COM_OSCAMPUS_NEXT'); ?>
            </span>
            <i class="fa fa-chevron-right"></i>
        </a>
    <?php endif; ?>
</div>

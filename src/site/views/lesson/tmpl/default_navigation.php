<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

/**
 * @var OscampusViewLesson $this
 */
$courseId = $this->model->getState('course.id');
if ($this->lesson->previous->id) {
    $previousLink = JHtml::_('osc.lesson.link', $this->lesson->previous, null, null, true);
}
if ($this->lesson->next->id) {
    $nextLink = JHtml::_('osc.lesson.link', $this->lesson->next, null, null, true);
}

JHtml::_('osc.lesson.navigation', $this->lesson);

?>
<div class="osc-btn-group hidden osc-lesson-navigation" id="course-navigation">
    <a href="<?php echo JHtml::_('osc.link.course', $courseId, null, null, true); ?>" class="osc-btn">
        <i class="fa fa-bars"></i>
        <span class="osc-hide-tablet">
            <?php echo JText::_('COM_OSCAMPUS_HOME'); ?>
        </span>
    </a><?php

    if (!empty($previousLink)) :
        ?><a href="<?php echo $previousLink; ?>" class="osc-btn" id="prevbut">
        <i class="fa fa-chevron-left"></i>
            <span class="osc-hide-tablet">
                <?php echo JText::_('COM_OSCAMPUS_PREVIOUS'); ?>
            </span>
        </a><?php
    endif;

    if (!empty($nextLink)) :
        ?><a href="<?php echo $nextLink; ?>" class="osc-btn" id="nextbut">
        <span class="osc-hide-tablet">
                <?php echo JText::_('COM_OSCAMPUS_NEXT'); ?>
            </span>
        <i class="fa fa-chevron-right"></i>
        </a>
        <?php
    endif;
    ?>
</div>

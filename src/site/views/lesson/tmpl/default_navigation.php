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
$courseId = $this->model->getState('course.id');

$linkBase = OscampusRoute::get('pathway');
$linkHome = "{$linkBase}&view=course&cid={$courseId}";
if ($this->lesson->index > 0) {
    $idx          = $this->lesson->index - 1;
    $linkPrevious = "{$linkBase}&view=lesson&cid={$courseId}&idx={$idx}";
}
if ($this->lesson->next) {
    $linkNext = "{$linkBase}&view=lesson&cid={$courseId}&idx={$this->lesson->next}";
}
?>
<div class="osc-btn-group hidden" id="course-navigation">
    <a href="<?php echo $linkHome; ?>" class="osc-btn">
        <i class="fa fa-bars"></i>
        <span class="osc-hide-tablet">
            <?php echo JText::_('COM_OSCAMPUS_HOME'); ?>
        </span>
    </a>

    <?php if (!empty($linkPrevious)) : ?>
        <a href="<?php echo $linkPrevious; ?>" class="osc-btn" id="prevbut">
            <i class="fa fa-chevron-left"></i>
            <span class="osc-hide-tablet">
                <?php echo JText::_('COM_OSCAMPUS_PREVIOUS'); ?>
            </span>
        </a>
    <?php endif; ?>

    <?php if (!empty($linkNext)) : ?>
        <a href="<?php echo $linkNext; ?>" class="osc-btn" id="nextbut">
            <span class="osc-hide-tablet">
                <?php echo JText::_('COM_OSCAMPUS_NEXT'); ?>
            </span>
            <i class="fa fa-chevron-right"></i>
        </a>
    <?php endif; ?>
</div>

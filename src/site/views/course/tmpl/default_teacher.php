<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

?>
<div id="content-teacher" class="osc-course-tabs-content" style="display: none">
    <?php
    echo JHtml::_('osc.teacher.links', $this->teacher);
    ?>
    <div class="osc-teacher-description">
        <?php echo JHtml::_('image', $this->teacher->image, $this->teacher->name, 'class="osc-teacher-image"'); ?>
        <h4><?php echo $this->teacher->name ?></h4>
        <?php echo $this->teacher->bio; ?>
    </div>
    <?php
    if ($this->teacher->courses) :
    ?>
    <h3><?php echo JText::_('COM_OSCAMPUS_TEACHER_COURSES'); ?></h3>
    <div class="osc-table">
        <div class="osc-section osc-row-heading osc-hide-tablet">
            <div class="block6">
                <i class="fa fa-bars"></i> <?php echo JText::_('COM_OSCAMPUS_TEACHER_NAME'); ?>
            </div>
            <div class="block3">
                <i class="fa fa-bolt"></i> <?php echo JText::_('COM_OSCAMPUS_COURSE_DIFFICULTY'); ?>
            </div>
            <div class="block3">
                <i class="fa fa-calendar"></i> <?php echo JText::_('COM_OSCAMPUS_COURSE_RELEASE_DATE') ;?>
            </div>
        </div>
        <?php
        foreach ($this->teacher->courses as $i => $course) :
            $courseLink = JRoute::_(OscampusRoute::get('courses') . '&view=course&cid=' . $course->id);
            ?>
            <div class="<?php echo 'osc-section ' . ($i % 2 ? 'osc-row-two' : 'osc-row-one'); ?>">
                <div class="block6">
                    <?php echo JHtml::_('link', $courseLink, $course->title); ?>
                </div>
                <div class="block3 osc-hide-tablet">
                    <?php echo JText::_('COM_OSCAMPUS_DIFFICULTY_' . $course->difficulty); ?>
                </div>
                <div class="block3 osc-hide-tablet">
                    <?php echo JHtml::_('date', $course->released, 'F j, Y'); ?>
                </div>
            </div>
            <?php
        endforeach;
        ?>
    </div>
    <?php
    endif;
    ?>
</div>
<!-- #content-teacher -->

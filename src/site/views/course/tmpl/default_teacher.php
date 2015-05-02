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
        <?php
        echo JHtml::_('image', $this->teacher->image, $this->teacher->name, 'class="osc-teacher-image"');
        echo $this->teacher->bio;
        ?>
    </div>
    <?php
    if ($this->teacher->courses) :
    ?>
    <h3><?php echo JText::_('COM_OSCAMPUS_TEACHER_COURSES'); ?></h3>
    <div class="osc-table">
        <div class="osc-section osc-row-heading osc-hide-tablet">
            <div class="block6">
                <?php echo JText::_('COM_OSCAMPUS_TEACHER_NAME'); ?>
            </div>
            <div class="block2">
                <?php echo JText::_('COM_OSCAMPUS_COURSE_DIFFICULTY'); ?>
            </div>
            <div class="block4">
                <?php echo JText::_('COM_OSCAMPUS_COURSE_RELEASE_DATE') ;?>
            </div>
        </div>
        <?php
        foreach ($this->teacher->courses as $i => $course) :
            ?>
            <div class="<?php echo 'osc-section ' . ($i % 2 ? 'osc-row-two' : 'osc-row-one'); ?>">
                <div class="block6">
                    <?php echo JHtml::_('link', "javascript:alert('Under Construction');", $course->title); ?>
                </div>
                <div class="block2 osc-hide-tablet">
                    <?php
                    echo JHtml::_('image', Juri::base() . '/media/com_oscampus/images/icon-' . $course->difficulty, '', 'class="osc-level-icon"'). ' ';
                    echo JText::_('COM_OSCAMPUS_DIFFICULTY_' . $course->difficulty);
                    ?>
                </div>
                <div class="block4 osc-hide-tablet">
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

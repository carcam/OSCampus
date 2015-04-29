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
    echo JHtml::_('image', $this->teacher->image, $this->teacher->name);
    echo $this->teacher->bio;

    if ($this->teacher->courses) :
    ?>
    <div class="osc-table">
        <?php
        foreach ($this->teacher->courses as $i => $course) :
            ?>
            <div class="<?php echo 'osc-section ' . ($i % 2 ? 'osc-row-two' : 'osc-row-one'); ?>">
                <div class="block6">
                    <?php echo JHtml::_('link', "javascript:alert('Under Construction');", $course->title); ?>
                </div>
                <div class="block2">
                    <?php echo JText::_('COM_OSCAMPUS_DIFFICULTY_' . $course->difficulty); ?>
                </div>
                <div class="block4">
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

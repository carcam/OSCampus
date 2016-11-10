<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();
/**
 * @var OscampusViewCourse $this
 */
$routing = OscampusRoute::getInstance();

$courseLinkBase = $routing->get('course');

?>
<div id="content-teacher" class="osc-course-tabs-content" style="display: none">
    <div class="osc-table">
        <div class="osc-section osc-row-heading">
            <div class="block6">
                <?php echo $this->teacher->name ?>
            </div>
            <div class="block6 a-right a-left-tablet">
                <?php echo JHtml::_('osc.teacher.links', $this->teacher); ?>
            </div>
        </div>
    </div>
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
                    <i class="fa fa-bars"></i> <?php echo JText::_('COM_OSCAMPUS_TEACHER_NAME'); ?>
                </div>
                <div class="block3">
                    <i class="fa fa-signal"></i> <?php echo JText::_('COM_OSCAMPUS_COURSE_DIFFICULTY'); ?>
                </div>
                <div class="block3">
                    <i class="fa fa-calendar"></i> <?php echo JText::_('COM_OSCAMPUS_COURSE_RELEASE_DATE'); ?>
                </div>
            </div>
            <?php
            foreach ($this->teacher->courses as $i => $course) :
                $courseLink = JRoute::_($courseLinkBase . '&cid=' . $course->id);
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

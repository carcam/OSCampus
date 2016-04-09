<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

$lesson = OscampusFactory::getContainer()->lesson;
$lesson->loadById($this->item->id);
?>
<div class="osc-section osc-course-list">
    <div class="block4 osc-course-image">
        <?php
        $link  = JHtml::_('osc.link.lessonid', $lesson->courses_id, $lesson->id, null, null, true);
        $style = sprintf(
            'style="background-image:url(%s); background-size: 100%% auto;"',
            $lesson->getThumbnail()
        );

        $image = JHtml::_('image', 'com_oscampus/lesson-placeholder.png', $lesson->title, $style, true);
        echo JHtml::_('link', $link, $image);
        ?>
    </div>
    <div class="block8 osc-course-description">
        <h2><?php echo JHtml::_('link', $link, $lesson->title); ?></h2>
        <?php echo JHtml::_('osc.link.course', $lesson->courses_id, $lesson->courseTitle); ?>
    </div>
</div>
<!-- .osc-section -->

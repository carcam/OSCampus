<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();
?>
<div id="content-requirements" class="osc-course-tabs-content" style="display: none">
    <?php
    if ($this->required) :
        ?>
        <ul>
            <?php
            foreach ($this->required as $course):
                $link = JRoute::_(OscampusRoute::get('courses') . '&view=course&cid=' . $course->id);
                ?>
                <li><?php echo JHtml::_('link', $link, $course->title); ?></li>
            <?php
            endforeach;
            ?>
        </ul>
    <?php
    else:
        ?>
        <p><?php echo JText::_('COM_OSCAMPUS_NO REQUIRED'); ?></p>
    <?php
    endif;
    ?>
</div>
<!-- #content-requirements -->

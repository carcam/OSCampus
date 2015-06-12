<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

/**
 * @var OscampusViewLesson $this
 */
defined('_JEXEC') or die();
?>
<div class="osc-container oscampus-wistia">
    <?php
    if (!empty($this->lesson->content->id)) :
        ?>
        <div class="osc-lesson-links">
            <?php echo $this->loadTemplate('navigation'); ?>
        </div>
        <!-- .osc-lesson-links -->

        <div class="osc-lesson-embed">
            <?php echo JHtml::_('osc.wistia.player', $this->lesson->content->id); ?>
        </div>
    <?php
    endif;
    ?>
</div>

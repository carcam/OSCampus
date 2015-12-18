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
<div class="osc-container oscampus-wistia" id="oscampus">
    <div class="osc-lesson-links">
        <?php echo $this->loadNavigation(); ?>
    </div>
    <!-- .osc-lesson-links -->

        <?php echo $this->lesson->render(); ?>
</div>

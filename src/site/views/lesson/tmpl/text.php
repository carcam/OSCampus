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
<div class="osc-container oscampus-text" id="oscampus">
    <div class="osc-section osc-lesson-links">
        <?php echo $this->loadNavigation(); ?>
    </div>
    <div class="osc-section">
        <?php echo $this->lesson->render(); ?>
    </div>
</div>

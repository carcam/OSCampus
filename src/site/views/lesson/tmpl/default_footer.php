<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

if ($this->lesson->footer) :
    ?>
    <div class="osc-section oscampus-lesson-footer">
        <?php echo $this->lesson->footer; ?>
    </div>
    <?php
endif;


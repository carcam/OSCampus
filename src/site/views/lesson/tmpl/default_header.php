<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

if ($this->lesson->header) : ?>
    <div class="osc-section oscampus-lesson-header">
        <?php echo $this->lesson->header; ?>
    </div>
    <?php
endif;

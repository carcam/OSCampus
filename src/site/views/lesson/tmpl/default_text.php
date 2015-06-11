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
<div class="osc-container oscampus-text">
    <?php
    echo $this->loadTemplate('navigation');
    echo $this->lesson->content;
    ?>
</div>

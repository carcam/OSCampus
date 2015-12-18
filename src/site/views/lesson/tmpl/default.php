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
<div class="osc-container oscampus-lesson" id="oscampus">
    <div class="page-header">
        <h1><?php echo $this->lesson->title; ?></h1>
    </div>

    <p><?php echo JText::sprintf('COM_OSCAMPUS_ERROR_LESSON_TYPE_UNDEFINED', $this->lesson->type); ?></p>
</div>

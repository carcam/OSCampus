<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();
?>
<div class="<?php echo $this->getPageClass('osc-container oscampus-lesson'); ?>" id="oscampus">
    <div class="page-header">
        <h1><?php echo $this->lesson->title; ?></h1>
    </div>

    <p><?php echo JText::sprintf('COM_OSCAMPUS_ERROR_LESSON_TYPE_UNDEFINED', $this->lesson->type); ?></p>
</div>

<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

/**
 * @var OscampusViewLesson $this
 */
defined('_JEXEC') or die();
?>
<div class="osc-container oscampus-text" id="oscampus">
    <div class="osc-section">
        <h1 class="osc-lesson-title"><?php echo $this->lesson->title; ?></h1>
        <div class="osc-lesson-links">
            <?php echo $this->loadNavigation(); ?>
        </div>
    </div>

    <?php echo $this->loadDefaultTemplate('header'); ?>

    <div class="osc-section oscampus-lesson-content">
        <?php echo $this->lesson->render(); ?>
    </div>

    <?php echo $this->loadDefaultTemplate('footer'); ?>

    <?php echo $this->loadDefaultTemplate('files'); ?>
</div>

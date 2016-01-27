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

$component = OscampusFactory::getApplication()->input->getCmd('tmpl') == 'component';

if (!$component):
    ?>
    <div class="osc-container oscampus-wistia" id="oscampus">
    <?php
endif;
?>
    <div class="osc-section">
        <h1 class="osc-lesson-title"><?php echo $this->lesson->title; ?></h1>
        <div class="osc-lesson-links">
            <?php echo $this->loadNavigation(); ?>
        </div>
    </div>
    <!-- .osc-lesson-links -->

<?php
if ($this->lesson->header):
    ?>
    <div class="osc-section oscampus-lesson-header">
        <?php echo $this->lesson->header; ?>
    </div>
    <?php
endif;
?>
    <div class="osc-section oscampus-lesson-content">
        <?php echo $this->lesson->render(); ?>
    </div>

<?php
if ($this->lesson->footer):
    ?>
    <div class="osc-section oscampus-lesson-footer">
        <?php echo $this->lesson->footer; ?>
    </div>
    <?php
endif;

echo $this->loadDefaultTemplate('files');

if (!$component):
    ?>
    </div>
    <?php
endif;

<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

$component = OscampusFactory::getApplication()->input->getCmd('tmpl') === 'oscampus';

if (!$component) :
    ?>
    <div class="osc-container oscampus-wistia" id="oscampus">
    <?php
endif;
?>
    <div class="osc-section">
        <h1 class="osc-lesson-title"><?php echo $this->lesson->title; ?></h1>
        <div class="osc-lesson-links">
            <?php echo $this->loadDefaultTemplate('navigation'); ?>
        </div>
    </div>
    <!-- .osc-lesson-links -->

    <div class="osc-section oscampus-lesson-content <?php echo $this->lesson->isAuthorised() ? 'osc-authorised-box': 'osc-signup-box'; ?>">
        <?php echo $this->lesson->render(); ?>
    </div>
<?php
if ($this->lesson->isAuthorised()) {
    echo $this->loadDefaultTemplate('description');
    echo $this->loadDefaultTemplate('files');

    echo OscampusHelper::renderModule('oscampus_lesson_bottom');
}

if (!$component) :
    ?>
    </div>
    <?php
endif;

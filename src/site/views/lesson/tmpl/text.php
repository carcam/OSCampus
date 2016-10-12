<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();
?>
<div class="osc-container oscampus-text" id="oscampus">
    <div class="osc-section">
        <h1 class="osc-lesson-title"><?php echo $this->lesson->title; ?></h1>
        <div class="osc-lesson-links">
            <?php echo $this->loadDefaultTemplate('navigation'); ?>
        </div>
    </div>

    <div class="osc-section oscampus-lesson-content <?php echo $this->lesson->isAuthorised() ? 'osc-authorised-box': 'osc-signup-box'; ?>">
        <?php echo $this->lesson->render(); ?>
    </div>

    <?php
    echo $this->loadDefaultTemplate('description');
    echo $this->loadDefaultTemplate('files');

    echo OscampusHelper::renderModule('oscampus_lesson_bottom');
    ?>
</div>

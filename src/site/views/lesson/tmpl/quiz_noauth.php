<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

/** @var OscampusViewLesson $this */
?>
<div class="osc-section">
    <h1 class="osc-lesson-title"><?php echo $this->lesson->title; ?></h1>
    <div class="osc-lesson-links">
        <?php echo $this->loadDefaultTemplate('navigation'); ?>
    </div>
</div>
<div class="osc-section oscampus-lesson-content osc-signup-box">
    <?php echo JHtml::_('image', 'com_oscampus/quiz-bg.jpg', 'OSCampus Quiz', null, true); ?>
</div>


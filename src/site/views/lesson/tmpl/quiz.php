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
/**
 * @var Oscampus\Quiz $quiz
 */
$this->lesson->content = new \Oscampus\Quiz($this->lesson->content);


$quiz = $this->lesson->content;
?>

<div class="osc-container oscampus-quiz">
    <?php echo $this->loadNavigation(); ?>
    <div class="osc-section osc-quiz-details">
        <div class="block4">
            <?php echo $quiz->startTimer(); ?>
        </div>
        <div class="block8">
            <div class="osc-quiz-right">
                <strong>Quiz time limit:</strong>
                <strong class="osc-positive-color"><?php echo (int)$quiz->timelimit; ?></strong> minutes
                <br/>
                <strong>Minimum score to pass this quiz:</strong>
                <strong class="osc-positive-color"><?php echo $quiz->score . '%'; ?></strong>
                <br/>
            </div>
        </div>
    </div>
    <!-- .osc-section -->

    <?php echo $this->loadTemplate('form'); ?>
</div>

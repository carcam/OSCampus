<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

use Oscampus\Lesson\Type\Quiz;
use OscampusViewLesson as View;

defined('_JEXEC') or die();

/**
 * @var View $this
 * @var Quiz $quiz
 */

$quiz     = $this->lesson->renderer;
$activity = $this->activity;

$attempt = $quiz->readAttempt($activity);
?>

<div class="osc-section">
    <h1 class="osc-lesson-title"><?php echo $this->lesson->title; ?></h1>
    <div class="osc-lesson-links">
        <?php echo $this->loadNavigation(); ?>
    </div>
</div>

<div class="osc-section osc-quiz-details">
    <div class="block1">
        <div class="osc-quiz-big-icon">
            <i class="fa fa-check"></i>
        </div>
    </div>
    <div class="block3">
        <div class="osc-quiz-left">
            <span class="osc-quiz-score-label">Your Score</span><br/>
            <span class="osc-quiz-percentage"><?php echo $attempt->score . '%'; ?></span><br/>
            <span class="osc-quiz-failed-label osc-positive-color">(Passed!)</span><br/>
        </div>
    </div>
    <div class="block8">
        <div class="osc-quiz-right">
            <strong>Your score:</strong> <strong
                class="osc-positive-color"><?php echo $attempt->score . '%'; ?></strong>, minimum score to pass is:
            <strong class="osc-positive-color"><?php echo $quiz->passingScore . '%'; ?></strong>.<br/>
            <strong>Congratulations! You passed!</strong>
        </div>
    </div>
</div>
<!-- .osc-section -->

<?php echo $this->loadTemplate('results');

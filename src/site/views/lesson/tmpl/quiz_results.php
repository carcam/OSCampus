<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
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
$attempt  = $quiz->readAttempt($activity);

if ($this->lesson->header) :
    ?>
    <div class="osc-section oscampus-lesson-header">
        <?php echo $this->lesson->header; ?>
    </div>
    <?php
endif;
?>

    <div class="osc-quiz-question">
        <?php
        $i           = 1;
        foreach ($attempt->questions as $question) :
            $selected = $question->selected;
            $correct = $selected && $question->answers[$selected]->correct;
            $icon    = $correct ? 'fa-check' : 'fa-times';
            ?>
            <h4><i class="<?php echo 'fa ' . $icon; ?>"></i> <?php echo $i . '. ' . $question->text; ?></h4>
            <ul class="osc-quiz-options">
                <?php
                foreach ($question->answers as $key => $answer) :
                    ?>
                    <li><?php echo $answer->text; ?></li>
                    <?php
                endforeach;
                ?>
            </ul>
        <?php endforeach; ?>
    </div>
    <!-- .osc-section -->

<?php
if ($this->lesson->footer) :
    ?>
    <div class="osc-section oscampus-lesson-footer">
        <?php echo $this->lesson->footer; ?>
    </div>
    <?php
endif;


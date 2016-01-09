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
/**
 * @var Oscampus\Lesson\Type\Quiz $quiz
 */
$quiz      = $this->lesson->render();
$questions = $quiz->getQuestions();

?>
<form id="quizForm" name="quizForm" action="javascript:alert('under construction');">
    <div class="osc-section oscampus-lesson-content">
        <?php
        $qn = 0;
        foreach ($questions as $qkey => $question):
            ?>
            <div class="<?php echo 'question' . ($qn++ % 2); ?> osc-quiz-question">
                <h4><?php echo sprintf('Q%s: %s', $qn, $question->text); ?></h4>
                <ul class="osc-quiz-options">
                    <?php
                    $an = 0;
                    foreach ($question->answers as $akey => $answer):
                        ?>
                        <li><input
                                type="radio"
                                name="<?php echo $qkey; ?>"
                                value="<?php echo $akey; ?>"/>
                            <?php echo $answer->text; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endforeach; ?>
    </div>
    <!-- .osc-section -->

    <div class="osc-section osc-quiz-submit">
        <button class="osc-btn osc-btn-main" type="submit">
            <?php echo JText::_('COM_OSCAMPUS_QUIZ_SUBMIT'); ?>
        </button>
    </div>
    <!-- .osc-section -->
</form>

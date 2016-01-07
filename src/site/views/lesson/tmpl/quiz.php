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
<div class="osc-container oscampus-quiz" id="oscampus">
    <div class="osc-section">
        <h1 class="osc-lesson-title"><?php echo $this->lesson->title; ?></h1>
        <div class="osc-lesson-links">
            <?php echo $this->loadNavigation(); ?>
        </div>
    </div>

    <div class="osc-section osc-quiz-details">
        <div class="block4">
            <div id="oscampus-timer" class="osc-quiz-left">
                <span class="osc-quiz-score-label">
                    <?php echo JText::_('COM_OSCAMPUS_QUIZ_TIME_LEFT'); ?>
                </span>
                <br/>
                <span class="osc-clock osc-quiz-percentage"></span>
                <br/>
            </div>
        </div>
        <div class="block4">
            <div class="osc-quiz-right">
                <strong><?php echo JText::_('COM_OSCAMPUS_QUIZ_PASSING_SCORE'); ?></strong>
                <strong class="osc-positive-color"><?php echo $quiz->passingScore . '%'; ?></strong>
            </div>
        </div>
    </div>

    <?php if ($this->lesson->header) : ?>
        <div class="osc-section oscampus-lesson-header">
            <?php echo $this->lesson->header; ?>
        </div>
    <?php endif; ?>

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

    <?php if ($this->lesson->footer) : ?>
        <div class="osc-section oscampus-lesson-footer">
            <?php echo $this->lesson->footer; ?>
        </div>
    <?php endif; ?>
</div>

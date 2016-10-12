<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */
use Oscampus\Lesson\Type\Quiz;
use OscampusViewLesson as View;

/**
 * @var OscampusViewLesson $this
 */
defined('_JEXEC') or die();
/**
 * @var View $this
 * @var Quiz $quiz
 */
$quiz      = $this->lesson->render();
$questions = $quiz->getQuestions();

?>
<div class="osc-section">
    <h1 class="osc-lesson-title"><?php echo $this->lesson->title; ?></h1>
    <div class="osc-lesson-links">
        <?php echo $this->loadDefaultTemplate('navigation'); ?>
    </div>
</div>

<div class="osc-section osc-quiz-details">
    <div class="block4">
        <div id="oscampus-timer" class="osc-quiz-left">
            <span class="osc-quiz-score-label">
                <?php echo JText::_('COM_OSCAMPUS_QUIZ_TIME_LEFT'); ?>
            </span>
            <br/>
            <span class="osc-clock osc-quiz-percentage">
                <?php echo $quiz->timeLimit . ':00'; ?>
            </span>
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

<form id="formQuiz" name="formQuiz" method="post" action="">
    <div class="osc-section oscampus-lesson-content <?php echo $this->lesson->isAuthorised() ? 'osc-authorised-box': 'osc-signup-box'; ?>">
        <?php
        $qn = 0;
        foreach ($questions as $qkey => $question):
            $name = 'questions[' . $qkey . ']';
            ?>
            <div class="<?php echo 'question' . ($qn++ % 2); ?> osc-quiz-question">
                <input type="hidden" name="<?php echo $name; ?>" value=""/>
                <h4><?php echo sprintf('Q%s: %s', $qn, $this->escape($question->text)); ?></h4>
                <ul class="osc-quiz-options">
                    <?php
                    $an = 0;
                    foreach ($question->answers as $akey => $answer):
                        $id   = $qkey . '_' . $akey;
                        ?>
                        <li>
                            <input
                                id="<?php echo $id; ?>"
                                type="radio"
                                name="<?php echo $name; ?>"
                                value="<?php echo $akey; ?>"/>
                            <?php echo $this->escape($answer->text); ?>
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

    <input type="hidden" name="option" value="com_oscampus"/>
    <input type="hidden" name="task" value="quiz.grade"/>
    <?php echo JHtml::_('form.token'); ?>
</form>

<?php
echo $this->loadDefaultTemplate('description');

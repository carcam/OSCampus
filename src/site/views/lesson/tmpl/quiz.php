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

    <div class="osc-section" style="text-align: center; margin: 15px;">
        <div class="block4">
            <div id="oscampus-timer">
                <div class="oscampus-timer-header" style="margin-bottom: 5px;">
                    <?php echo JText::_('COM_OSCAMPUS_QUIZ_TIME_LEFT'); ?>
                </div>
                <div class="oscampus-timer-countdown" style="font-size: 50px;">
                    10:00
                </div>
            </div>
        </div>
        <div class="block4">
            <span style="font-weight: bold;"><?php echo JText::_('COM_OSCAMPUS_QUIZ_PASSING_SCORE'); ?></span>
            <?php echo $quiz->passingScore . '%'; ?>
        </div>
    </div>

    <?php if ($this->lesson->header) : ?>
        <div class="osc-section oscampus-lesson-header">
            <?php echo $this->lesson->header; ?>
        </div>
    <?php endif; ?>

    <div class="osc-section oscampus-lesson-content">
        <ul>
            <?php
            $qn = 0;
            foreach ($questions as $qkey => $question):
                ?>
                <li class="<?php echo 'question' . ($qn++ % 2); ?>">
                    <span><?php echo sprintf('Q%s: %s', $qn, $question->text); ?></span>
                    <ul>
                        <?php
                        $an = 0;
                        foreach ($question->answers as $akey => $answer):
                            ?>
                            <li><input
                                    type="radio"
                                    name="<?php echo $qkey; ?>"
                                    value="<?php echo $akey; ?>"/>
                                <label><?php echo $answer->text; ?></label>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <?php if ($this->lesson->footer) : ?>
        <div class="osc-section oscampus-lesson-footer">
            <?php echo $this->lesson->footer; ?>
        </div>
    <?php endif; ?>
</div>

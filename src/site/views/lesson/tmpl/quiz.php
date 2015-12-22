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
    <div class="osc-section osc-lesson-links">
        <?php echo $this->loadNavigation(); ?>
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
                    <span><?php echo sprintf('Q%s: %s', $qn, $question->text);?></span>
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

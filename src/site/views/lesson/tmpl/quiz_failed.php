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
?>
<div class="osc-container oscampus-quiz" id="oscampus">
    <div class="osc-section">
        <h1 class="osc-lesson-title"><?php echo $this->lesson->title; ?></h1>
        <div class="osc-lesson-links">
            <?php echo $this->loadNavigation(); ?>
        </div>
    </div>

    <div class="osc-section osc-quiz-details">
        <div class="block1">
            <div class="osc-quiz-big-icon">
                <i class="fa fa-times"></i>
            </div>
        </div>
        <div class="block3">
            <div class="osc-quiz-left">
                <span class="osc-quiz-score-label">
                    <?php echo JText::_('COM_OSCAMPUS_QUIZ_YOUR_SCORE'); ?>
                </span>
                        <br/>
                <span class="osc-quiz-percentage">
                    <?php echo $activity->score . '%'; ?>
                </span>
                        <br/>
                <span class="osc-quiz-failed-label osc-negative-color">
                    <?php echo JText::_('COM_OSCAMPUS_QUIZ_STATUS_FAILED'); ?>
                </span>
                <br/>
            </div>
        </div>
        <div class="block8">
            <div class="osc-quiz-right">
                <strong>Your score:</strong> <strong
                    class="osc-negative-color"><?php echo $activity->score . '%'; ?></strong>, minimum score to pass
                is:
                <strong class="osc-positive-color"><?php echo $quiz->passingScore . '%'; ?></strong>.<br/>
                <strong>Would you like to take it again now?</strong><br/>

                <div class="osc-btn-group">
                    <form id="formRetry" name="formRetry" action="" method="post">
                        <input type="hidden" name="retry" value="1"/>
                        <div class="osc-btn-group">
                            <button class="osc-btn osc-btn-main">
                                <?php echo JText::_('COM_OSCAMPUS_QUIZ_RETRY'); ?>
                            </button><?php
                            echo JHtml::_(
                                'osc.link.course',
                                $this->lesson->pathways_id,
                                $this->lesson->courses_id,
                                JText::_('COM_OSCAMPUS_LATER'),
                                'class="osc-btn"'
                            );
                            ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- .osc-section -->
</div>
<?php echo $this->loadTemplate('results');

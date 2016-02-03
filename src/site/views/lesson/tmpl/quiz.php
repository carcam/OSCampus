<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
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

$quiz  = $this->lesson->renderer;
$retry = OscampusFactory::getApplication()->input->getInt('retry', 0);

if ($this->activity->data && !$retry) {
    if ($this->activity->score < $quiz->passingScore) {
        $quizTemplate = 'failed';
    } else {
        $quizTemplate = 'passed';
    }
} else {
    $quizTemplate = 'form';
}
?>
<div class="osc-container oscampus-quiz" id="oscampus">
    <?php
    if ($this->lesson->isAuthorised()) {
        echo $this->loadTemplate($quizTemplate);
        echo $this->loadDefaultTemplate('files');
    } else {
        echo '<div class="osc-section oscampus-lesson-content osc-signup-box"><img src="' . Juri::base() . 'media/com_oscampus/images/quiz-bg.jpg" alt="OSCampus Quiz" /></div>';
    }
    ?>
</div>

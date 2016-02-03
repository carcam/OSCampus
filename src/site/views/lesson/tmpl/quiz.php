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
    <div class="osc-section oscampus-lesson-content <?php echo $this->lesson->isAuthorised() ? 'osc-authorised-box': 'osc-signup-box'; ?>">
        <?php
        echo $this->loadTemplate($quizTemplate);
        echo $this->loadDefaultTemplate('files');
        ?>
    </div>
</div>

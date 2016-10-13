<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */
use Oscampus\Lesson\Type\Quiz;
use OscampusViewLesson as View;

defined('_JEXEC') or die();
/**
 * @var Quiz $quiz
 */

$quiz  = $this->lesson->renderer;
$retry = OscampusFactory::getApplication()->input->getInt('retry', 0);

$quizTemplate = 'noauth';
if ($this->lesson->isAuthorised()) {
    if ($this->activity->data && !$retry) {
        if ($this->activity->score < $quiz->passingScore) {
            $quizTemplate = 'failed';
        } else {
            $quizTemplate = 'passed';
        }
    } else {
        $quizTemplate = 'form';
    }
}
?>
<div class="osc-container oscampus-quiz" id="oscampus">
    <?php
    echo $this->loadTemplate($quizTemplate);

    if ($this->lesson->isAuthorised()) {
        echo $this->loadDefaultTemplate('files');
    }
    echo OscampusHelper::renderModule('oscampus_lesson_bottom');
    ?>
</div>

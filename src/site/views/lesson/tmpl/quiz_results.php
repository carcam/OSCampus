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
$attempt  = $quiz->getLastAttempt($this->activity);

echo $this->loadDefaultTemplate('header');
?>
    <div class="osc-quiz-question">
        <?php
        $i           = 1;
        foreach ($attempt as $question) :
            $selected = $question->selected;
            $correct = $selected && $question->answers[$selected]->correct;
            $icon    = $correct ? 'fa-check' : 'fa-times';
            ?>
            <h4><i class="<?php echo 'fa ' . $icon; ?>"></i> <?php echo $i++ . '. ' . $question->text; ?></h4>
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
echo $this->loadDefaultTemplate('footer');

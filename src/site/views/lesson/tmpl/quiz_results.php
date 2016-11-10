<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Oscampus\Lesson\Type\Quiz;

defined('_JEXEC') or die();

/**
 * @var Quiz $quiz
 */

$quiz    = $this->lesson->renderer;
$attempt = $quiz->getLastAttempt($this->activity);

?>
    <div class="osc-quiz-question">
        <?php
        $i           = 1;
        foreach ($attempt as $question) :
            $selected = $question->selected;
            $correct = $selected && $question->answers[$selected]->correct;
            ?>
            <h4>
                <?php
                echo sprintf('<i class="fa %s"></i> ', $correct ? 'fa-check' : 'fa-times');
                echo $i++ . '. ' . $this->escape($question->text);
                ?>
            </h4>
            <ul class="osc-quiz-options">
                <?php
                foreach ($question->answers as $key => $answer) :
                    ?>
                    <li>
                        <?php
                        echo sprintf('<i class="fa %s"></i> ', $answer->correct ? 'fa-check' : 'fa-fw');
                        echo $this->escape($answer->text);
                        ?>
                    </li>
                    <?php
                endforeach;
                ?>
            </ul>
        <?php endforeach; ?>
    </div>
    <!-- .osc-section -->

<?php
echo $this->loadDefaultTemplate('description');

<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

?>
<h2>Form</h2>

<form name="osc_quiz" method="post" action="">
    <?php foreach ($this->lesson->content->questions as $idx => $question): ?>
        <div class="osc-quiz-question">
            <h4><?php echo $question->text; ?></h4>
            <ul class="osc-quiz-options">
                <?php foreach ($question->choices as $choice): ?>
                    <li>
                        <input
                            type="radio"
                            name="<?php echo "answers[{$idx}]"; ?>"
                            value="<?php echo $choice->text; ?>"/>
                        <?php echo $choice->text; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endforeach; ?>
    <!-- .osc-section -->

    <div>
        <button type="submit" class="osc-btn osc-btn-main">Submit</button>
    </div>
</form>

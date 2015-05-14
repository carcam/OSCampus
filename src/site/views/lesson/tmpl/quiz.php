<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();
?>

<div class="osc-container oscampus-quiz">

    <div class="page-header">
        <h1>Quiz name</h1>
    </div>

    <div class="osc-section osc-quiz-details">
        <div class="block4">
            <div class="osc-quiz-left">
                <span class="osc-quiz-time-left">Time left</span><br/>
                <span class="osc-quiz-time">04:30</span><br/>
                <span class="osc-quiz-time-labels">Minutes Seconds</span><br/>
            </div>
        </div>
        <div class="block8">
            <div class="osc-quiz-right">
                <strong>Quiz time limit:</strong> <strong class="osc-label-color">10</strong> minutes<br/>
                <strong>Minimum score to pass this quiz:</strong> <strong class="osc-label-color">70%</strong><br/>
                <strong>This quiz can be taken:</strong> <strong class="osc-label-color">Unlimited</strong> times<br/>
            </div>
        </div>
    </div>
    <!-- .osc-section -->

    <h2>Form</h2>
    <form name="osc_quiz" method="post" action="">
        <div class="osc-quiz-question">
            <h4>1. What is the right answer here?</h4>
            <ul class="osc-quiz-options">
                <li><input type="radio" name="q1" value="a"> This is the answer one</li>
                <li><input type="radio" name="q1" value="b"> This is the answer two</li>
                <li><input type="radio" name="q1" value="c"> This is the answer three</li>
                <li><input type="radio" name="q1" value="d"> This is the answer four</li>
            </ul>
        </div>
        <!-- .osc-section -->

        <div>
            <button type="submit" class="osc-btn">Submit</button>
        </div>
    </form>

    <h2>Right answer</h2>
    <div class="osc-quiz-question">
        <h4><i class="fa fa-check"></i> 1. What is the right answer here?</h4>
        <ul class="osc-quiz-options">
            <li>This is the answer one</li>
            <li>This is the answer two</li>
            <li>This is the answer three</li>
            <li>This is the answer four</li>
        </ul>
    </div>
    <!-- .osc-section -->

    <h2>Wrong answer</h2>
    <div class="osc-quiz-question">
        <h4><i class="fa fa-times"></i> 1. What is the right answer here?</h4>
        <ul class="osc-quiz-options">
            <li>This is the answer one</li>
            <li>This is the answer two</li>
            <li>This is the answer three</li>
            <li>This is the answer four</li>
        </ul>
    </div>
    <!-- .osc-section -->

</div>
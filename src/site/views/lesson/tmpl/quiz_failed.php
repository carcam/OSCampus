<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();
?>
<h2>Failed quiz</h2>

<div class="osc-section osc-quiz-details">
    <div class="block1">
        <div class="osc-quiz-big-icon">
            <i class="fa fa-times"></i>
        </div>
    </div>
    <div class="block3">
        <div class="osc-quiz-left">
            <span class="osc-quiz-score-label">Your Score</span><br/>
            <span class="osc-quiz-percentage">59%</span><br/>
            <span class="osc-quiz-failed-label osc-negative-color">(Failed)</span><br/>
        </div>
    </div>
    <div class="block8">
        <div class="osc-quiz-right">
            <strong>Your score:</strong> <strong class="osc-negative-color">28%</strong>, minimum score to pass is:
            <strong class="osc-positive-color">70%</strong>.<br/>
            <strong>Would you like to take it again now?</strong><br/>

            <div class="osc-btn-group">
                <button type="submit" class="osc-btn osc-btn-main">Yes, I want to try again!</button>
                <button type="submit" class="osc-btn">Later</button>
            </div>
        </div>
    </div>
</div>
<!-- .osc-section -->

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

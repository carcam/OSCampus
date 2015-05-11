<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();
?>
<h1>Under Construction</h1>
This will be specific lessons. Right now this means either (login at ostraining.com required)

<ul>
    <li>
        <a href="https://www.ostraining.com/courses/session/wordpress/why/videos/introduction/" target="_blank">
            a video
        </a>
        or
    </li>
    <li>
        <a href="https://www.ostraining.com/courses/session/wordpress/why/videos/quiz/" target="_blank: ">
            a quiz
        </a>
    </li>
</ul>

<div class="osc-container oscampus-lesson">

    <div class="page-header">
        <h1>Lesson name</h1>
    </div>

    <div class="osc-lesson-links">
        <div class="osc-section">
            <div class="block6">
                <div class="osc-button-group wistia_buttons_container" id="wistia_NaN_buttons_container">
                    <a href="#" class="osc-btn-main wistia_button download hidden" id="wistia_NaN_download_button">
                        <i class="fa fa-cloud-download"></i>
                        <span class="uk-hidden-small uk-hidden-medium"> Download</span>
                    </a>
                    <a href="#" class="osc-btn-main wistia_button autoplay hidden" id="wistia_NaN_autoplay_button">
                        <i class="fa fa-check"></i>
                        <span class="uk-hidden-small uk-hidden-medium">Autoplay</span>
                    </a>
                    <a href="#" class="osc-btn-main osc-btn-active wistia_button focus hidden" id="wistia_NaN_focus_button">
                        <i class="fa fa-times"></i>
                        <span class="uk-hidden-small uk-hidden-medium">Focus</span>
                    </a>
                </div>
            </div>
            <div class="block6">
                <div class="osc-button-group hidden" id="course-navigation">
                    <a href="#" class="osc-btn-main">
                        <i class="fa fa-bars"></i>
                        <span class="uk-hidden-small uk-hidden-medium">Home</span>
                    </a>
                    <a href="#" class="osc-btn-main" id="prevbut">
                        <i class="fa fa-angle-left"></i> 
                        <span class="uk-hidden-small uk-hidden-medium">Prev</span>
                    </a>
                    <a href="#" class="osc-btn-main" id="nextbut">
                        <span class="uk-hidden-small uk-hidden-medium">Next</span>
                        <i class="fa fa-angle-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- .osc-section -->

    <div class="osc-lesson-embed">
        <?php echo JHTML::_('content.prepare', '{wistia}bbql2oyrgi{/wistia}'); ?>
    </div>

</div>
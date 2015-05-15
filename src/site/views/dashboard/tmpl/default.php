<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

JHtml::_('osc.tabs', '.osc-course-tabs div');
?>

<div class="osc-container oscampus-dashboard" id="oscampus">

    <div class="page-header">
        <h1>Dashboard</h1>
    </div>

    <div class="osc-section osc-dashboard-announcements">
        <div class="block3">
            You've completed <strong>3 certificates</strong> and watched <strong>387 videos</strong>
        </div>
        <div class="block3">
            The most watched class past week was <a href="javascript:void(0)">Gimp 4: Fills, Gradients, Masking</a>
        </div>
        <div class="block3">
            Do you need help? <a href="javascript:void(0)">Ask for assitance in the forum</a>
        </div>
        <div class="block3 osc-dashboard-block-featured">
            Sign up in Alledia, <strong>30% off</strong> with coupon <strong>OSTMEMBER</strong>
        </div>
    </div>
    <!-- .osc-section -->

    <div class="osc-section osc-dashboard-tabs">
        <div class="block4 osc-dashboard-tabs-left">
            <div class="osc-section osc-course-tabs">
                <div data-content="#content-my-classes" class="block12">
                    <i class="fa fa-mortar-board"></i> My Classes
                </div>
                <div data-content="#content-my-certificates" class="block12 osc-tab-disabled">
                    <i class="fa fa-certificate"></i> My Certificates
                </div>
                <div data-content="#content-my-forum" class="block12 osc-tab-disabled">
                    <i class="fa fa-life-ring"></i> My Forum Topics
                </div>
                <div data-content="#content-my-stats" class="block12 osc-tab-disabled">
                    <i class="fa fa-line-chart"></i> My Stats
                </div>
                <div data-content="#content-new-classes" class="block12 osc-tab-disabled">
                    <i class="fa fa-plus-circle"></i> New Classes
                </div>
                <div data-content="#content-new-blog" class="block12 osc-tab-disabled">
                    <i class="fa fa-pencil-square-o"></i> New on the Blog
                </div>
            </div>
            <!-- .osc-section -->
        </div>
        <div class="block8 osc-dashboard-tabs-right">
            <div id="content-my-classes" class="osc-course-tabs-content">
                <ul>
                    <li><a href="javascript:void(0)">Lorem ipsum dolor sit ame consecteur</a></li>
                    <li><a href="javascript:void(0)">Lorem ipsum dolor sit ame consecteur</a></li>
                    <li><a href="javascript:void(0)">Lorem ipsum dolor sit ame consecteur</a></li>
                    <li><a href="javascript:void(0)">Lorem ipsum dolor sit ame consecteur</a></li>
                    <li><a href="javascript:void(0)">Lorem ipsum dolor sit ame consecteur</a></li>
                    <li><a href="javascript:void(0)">See all the available classes</a></li>
                </ul>
            </div>
            <!-- #content-my-classes -->

            <div id="content-my-certificates" class="osc-course-tabs-content osc-course-tabs-content-right" style="display: none">
                My Certificates
            </div>
            <!-- #content-my-certificates -->

            <div id="content-my-forum" class="osc-course-tabs-content osc-course-tabs-content-right" style="display: none">
                My Forum Topics
            </div>
            <!-- #content-my-forum -->

            <div id="content-my-stats" class="osc-course-tabs-content osc-course-tabs-content-right" style="display: none">
                My Stats
            </div>
            <!-- #content-my-stats -->

            <div id="content-new-classes" class="osc-course-tabs-content osc-course-tabs-content-right" style="display: none">
                New Classes
            </div>
            <!-- #content-new-classes -->

            <div id="content-new-blog" class="osc-course-tabs-content osc-course-tabs-content-right" style="display: none">
                New on the Blog
            </div>
            <!-- #content-new-blog -->
        </div>
    </div>
    <!-- .osc-section -->

</div>
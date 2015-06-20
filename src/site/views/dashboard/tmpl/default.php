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
        <h1><?php echo JText::_('COM_OSCAMPUS_DASHBOARD'); ?></h1>
    </div>

    <?php echo $this->loadTemplate('announcements'); ?>

    <div class="osc-section osc-dashboard-tabs">
        <div class="block4 osc-dashboard-tabs-left">
            <div class="osc-section osc-course-tabs">
                <div data-content="#content-my-classes" class="block12">
                    <i class="fa fa-mortar-board"></i>
                    <?php echo JText::_('COM_OSCAMPUS_DASHBOARD_COURSES'); ?>
                </div>
                <div data-content="#content-my-certificates" class="block12 osc-tab-disabled">
                    <i class="fa fa-certificate"></i>
                    <?php echo JText::_('COM_OSCAMPUS_DASHBOARD_CERTIFICATES'); ?>
                </div>
                <div data-content="#content-my-forum" class="block12 osc-tab-disabled">
                    <i class="fa fa-life-ring"></i>
                    <?php echo JText::_('COM_OSCAMPUS_DASHBOARD_KUNENA'); ?>
                </div>
                <div data-content="#content-my-stats" class="block12 osc-tab-disabled">
                    <i class="fa fa-line-chart"></i>
                    <?php echo JText::_('COM_OSCAMPUS_DASHBOARD_STATS'); ?>
                </div>
                <div data-content="#content-new-classes" class="block12 osc-tab-disabled">
                    <i class="fa fa-plus-circle"></i>
                    <?php echo JText::_('COM_OSCAMPUS_DASHBOARD_LATEST_COURSES'); ?>
                </div>
                <div data-content="#content-new-blog" class="block12 osc-tab-disabled">
                    <i class="fa fa-pencil-square-o"></i>
                    <?php echo JText::_('COM_OSCAMPUS_DASHBOARD_NEW_BLOG'); ?>
                </div>
            </div>
            <!-- .osc-section -->
        </div>

        <div class="block8 osc-dashboard-tabs-right">
            <?php
            echo $this->loadTemplate('courses');
            echo $this->loadTemplate('certificates');
            echo $this->loadTemplate('kunena');
            echo $this->loadTemplate('stats');
            echo $this->loadTemplate('latest');
            echo $this->loadTemplate('blog');
            ?>
        </div>
    </div>
    <!-- .osc-section -->

</div>

<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

if ($this->lessons) :
    ?>
    <div>
        <?php echo JText::plural('COM_OSCAMPUS_SEARCH_RESULTS_LESSONS', count($this->lessons)); ?>
    </div>
    <?php
    foreach ($this->lessons as $item) :
        ?>
        <div class="osc-section osc-course-list">
            <div class="block4 osc-course-image">
                <?php
                $link  = JHtml::_('osc.link.lessonid', $item->courses_id, $item->id, null, null, true);
                $image = JHtml::_('image', \Oscampus\Course::DEFAULT_IMAGE, $item->title);
                echo JHtml::_('link', $link, $image);
                ?>
            </div>
            <div class="block8 osc-course-description">
                <h2><?php echo JHtml::_('link', $link, $item->title); ?></h2>
            </div>
        </div>
        <!-- .osc-section -->

        <?php
    endforeach;

else :
    ?>
    <div class="osc-alert-notify"><i class="fa fa-info-circle"></i>
        <?php echo JText::plural('COM_OSCAMPUS_SEARCH_RESULTS_LESSONS', 0); ?>
    </div>
    <?php
endif;

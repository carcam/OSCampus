<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

if ($this->courses) :
    ?>
    <div class="osc-alert-success m-bottom"><i class="fa fa-info-circle"></i>
        <?php echo JText::plural('COM_OSCAMPUS_SEARCH_RESULTS_COURSES', count($this->courses)); ?>
    </div>
    <?php
    foreach ($this->courses as $this->item) :
        // Use sub-template from pathway view
        echo $this->loadViewTemplate('pathway', 'course');
    endforeach;

else :
    ?>
    <div class="osc-alert-warning m-bottom"><i class="fa fa-info-circle"></i>
        <?php echo JText::plural('COM_OSCAMPUS_SEARCH_RESULTS_COURSES', 0); ?>
    </div>
    <?php
endif;

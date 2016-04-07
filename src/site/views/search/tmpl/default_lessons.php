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
    foreach ($this->lessons as $this->item) :
        echo '<br/>' . $this->item->title;
    endforeach;

else :
    ?>
    <div class="osc-alert-notify"><i class="fa fa-info-circle"></i>
        <?php echo JText::plural('COM_OSCAMPUS_SEARCH_RESULTS_LESSONS', 0); ?>
    </div>
    <?php
endif;

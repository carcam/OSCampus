<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

if ($this->pathways) :
    ?>
    <div>
        <?php echo JText::plural('COM_OSCAMPUS_SEARCH_RESULTS_PATHWAYS', count($this->pathways)); ?>
    </div>
    <?php
    foreach ($this->pathways as $this->item) :
        // Use sub-template from pathway view
        echo $this->loadViewTemplate('pathways', 'pathway');
    endforeach;

else :
    ?>
    <div class="osc-alert-notify"><i class="fa fa-info-circle"></i>
        <?php echo JText::plural('COM_OSCAMPUS_SEARCH_RESULTS_PATHWAYS', 0); ?>
    </div>
    <?php
endif;

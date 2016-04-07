<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

/** @var OscampusViewSearch $this */
?>
<div class="<?php echo $this->getPageClass('osc-container oscampus-search'); ?>" id="oscampus">
    <?php
    if ($heading = $this->getHeading('COM_OSCAMPUS_HEADING_SEARCH')) :
        ?>
        <div class="page-header">
            <h1><?php echo $heading; ?></h1>
        </div>
        <?php
    endif;

    $types = (array)$this->model->getState('show.types');

    if (!$types || in_array('P', $types)) :
        echo $this->loadTemplate('pathways');
    endif;

    if (!$types || in_array('C', $types)) :
        echo $this->loadTemplate('courses');
    endif;
    ?>
</div>


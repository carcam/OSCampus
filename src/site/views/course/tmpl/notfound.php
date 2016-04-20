<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

/**
 * @var OscampusViewCourse $this
 */

$heading = $this->getHeading();
?>
<div class="<?php echo $this->getPageClass('osc-container oscampus-course'); ?>" id="oscampus">
    <?php
    if ($heading) :
        ?>
        <div class="page-header">
            <h1><?php echo $heading; ?></h1>
        </div>
        <?php
    endif;
    ?>

    <div class="osc-alert-warning m-bottom"><i class="fa fa-info-circle"></i>
        <?php echo JText::_('COM_OSCAMPUS_ERROR_COURSE_NOT_FOUND'); ?>
    </div>
</div>


<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

/** @var OscampusViewMycourses $this */
?>
<div class="<?php echo $this->getPageClass('osc-container oscampus-pathways'); ?>" id="oscampus">
    <?php if ($heading = $this->getHeading('COM_SIMPLERENEW_HEADING_MYCOURSES')): ?>
        <div class="page-header">
            <h1><?php echo $heading; ?></h1>
        </div>
    <?php endif; ?>

    <table>
        <thead>
        <tr>
            <th><?php echo JText::_('COM_OSCAMPUS_COURSE_TITLE'); ?></th>
            <th><?php echo JText::_('COM_OSCAMPUS_LAST_VISIT_DATE'); ?></th>
            <th><?php echo JText::_('COM_OSCAMPUS_COURSE_PROGRESS'); ?></th>
        </tr>
        </thead>

        <tbody>
        <?php foreach ($this->items as $item) : ?>
            <tr>
                <td><?php echo JHtml::_('osc.course.link', $item); ?></td>
                <td><?php echo $item->last_lesson->format('Y-m-d'); ?></td>
                <td>TBD</td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

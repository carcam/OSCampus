<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();
/**
 * @var OscampusViewDashboard $this
 */
$total = $this->model->getState('list.size');
$max   = min(count($this->courses), $total);
?>
<div id="content-my-classes" class="osc-course-tabs-content">
    <ul>
        <?php for ($i = 0; $i < $max; $i++) : ?>
            <li><?php echo JHtml::_('link', 'javascript:void(0)', $this->courses[$i]->title); ?></a></li>
        <?php endfor; ?>
        <li><?php echo JHtml::_('link', JURI::current() . '?layout=courses', 'See all the available classes'); ?></li>
    </ul>
</div>

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

$total = count($this->certificates);
$max   = min($total, $this->model->getState('list.size'));
?>
<div id="content-my-certificates" class="osc-course-tabs-content osc-course-tabs-content-right" style="display: none">
    <ul>
        <?php for ($i=0; $i<$max; $i++) : ?>
        <li><?php echo JHtml::_('link', 'javascript:void(0)', $this->certificates[$i]->title); ?></a></li>
        <?php endfor; ?>
        <?php if ($total > $max) : ?>
        <li><?php echo JHtml::_('link', JURI::current().'?layout=certificates', 'See all my certificates'); ?></a></li>
        <?php endif; ?>
    </ul>
</div>
<!-- #content-my-certificates -->

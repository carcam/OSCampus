<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();
?>
<div id="content-my-classes" class="osc-course-tabs-content">
    <ul>
        <?php foreach ($this->courses as $course) : ?>
            <li><?php echo JHtml::_('link', 'javascript:void(0)', $course->title); ?></a></li>
        <?php endforeach; ?>
    </ul>
</div>

<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

/**
 * @var JLayoutFile $this
 * @var object      $displayData
 * @var string      $layoutOutput
 * @var string      $path
 */

$item = $displayData;
?>
<div class="osc-section osc-course-list">
    <div class="block4 osc-course-image">
        <?php
        $link  = JHtml::_('osc.pathway.link', $item, null, null, true);
        $image = JHtml::_('image', $item->image, $item->title);
        echo JHtml::_('link', $link, $image);
        ?>
    </div>
    <div class="block8 osc-course-description">
        <h2><?php echo JHtml::_('link', $link, $item->title); ?></h2>

        <?php echo $item->description; ?>
    </div>
</div>
<!-- .osc-section -->

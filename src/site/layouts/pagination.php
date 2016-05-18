<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

/**
 * @var JLayoutFile $this
 * @var array       $displayData
 * @var string      $layoutOutput
 * @var string      $path
 */

if (!empty($displayData['list'])) {
    $list = $displayData['list'];
    if (!empty($list['pages'])) {
        $pages = $list['pages'];
    }
}

if (!empty($pages)) {
    $displayLink = function (JPaginationObject $page, $title = null) {
        $text = $title ?: $page->text;
        if ($page->active) {
            return '<li><span>' . $text . '</span></li>';
        }

        return '<li>' . JHtml::_('link', $page->link, $text) . '</li>';
    };
    ?>
    <div class="osc-pagination">
        <ul class="osc-pagination-list">
            <?php
            if ($pages['start']['active']) {
                echo $displayLink($pages['start']['data'], '<i class="fa fa-angle-double-left"></i>');
            }
            if ($pages['previous']['active']) {
                echo $displayLink($pages['previous']['data'], '<i class="fa fa-chevron-left"></i>');
            }

            foreach ($pages['pages'] as $page) {
                echo $displayLink($page['data']);
            }

            if ($pages['next']['active']) {
                echo $displayLink($pages['next']['data'], '<i class="fa fa-chevron-right"></i>');
            }
            if ($pages['end']['active']) {
                echo $displayLink($pages['end']['data'], '<i class="fa fa-angle-double-right"></i>');
            }
            ?>
        </ul>
    </div>
    <?php
}

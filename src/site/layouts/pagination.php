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
            return '<li class="active hidden-phone"><span>' . $text . '</span></li>';
        }

        return '<li class="hidden-phone">' . JHtml::_('link', $page->link, $text) . '</li>';
    };
    ?>
    <div class="pagination">
        <ul class="pagination-list">
            <?php
            if ($pages['start']['active']) {
                echo $displayLink($pages['start']['data'], '<i class="icon-first"></i>');
            }
            if ($pages['previous']['active']) {
                echo $displayLink($pages['previous']['data'], '<i class="icon-previous"></i>');
            }

            foreach ($pages['pages'] as $page) {
                echo $displayLink($page['data']);
            }

            if ($pages['next']['active']) {
                echo $displayLink($pages['next']['data'], '<i class="icon-next"></i>');
            }
            if ($pages['end']['active']) {
                echo $displayLink($pages['end']['data'], '<i class="icon-last"></i>');
            }
            ?>
        </ul>
    </div>
    <?php
}

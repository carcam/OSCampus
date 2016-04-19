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
 * @var JPagination $displayData
 * @var string      $layoutOutput
 * @var string      $path
 */

$pages = $displayData->getPaginationPages();

$displayLink = function (JPaginationObject $page, $title = null) {
    $text = $title ?: $page->text;
    if ($page->link) {
        $text = sprintf('<a href="%s">%s</a>', $page->link, $text);
    } else {
        $text = '<span>' . $text . '</span>';
    }

    $class = $page->active ? ' class="uk-active"' : '';

    $html = '<li' . $class . '>' . $text . '</li>';

    echo $html;
};

?>
<div class="pagination">
    <ul class="uk-pagination">
        <?php
        if ($pages['start']['active']) {
            echo $displayLink($pages['start']['data'], '<i class="uk-icon-angle-double-left"></i>');
        }
        if ($pages['previous']['active']) {
            echo $displayLink($pages['previous']['data'], '<i class="uk-icon-angle-left"></i>');
        }

        foreach ($pages['pages'] as $page) {
            echo $displayLink($page['data']);
        }

        if ($pages['next']['active']) {
            echo $displayLink($pages['next']['data'], '<i class="uk-icon-angle-right"></i>');
        }
        if ($pages['end']['active']) {
            echo $displayLink($pages['end']['data'], '<i class="uk-icon-angle-double-right"></i>');
        }
        ?>
    </ul>
</div>

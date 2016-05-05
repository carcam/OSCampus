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

// Normalize the uri for reliability
$uri = JUri::getInstance();
$vars = array_filter($uri->getQuery(true));
if (isset($vars['start'])) {
    unset($vars['start']);
}
if (isset($vars['limitstart'])) {
    unset($vars['limitstart']);
}
$uri->setQuery($vars);

$pages = $displayData->getPaginationPages();

$displayLink = function (JPaginationObject $page, $title = null) use ($uri) {
    $text = $title ?: $page->text;
    if (!$page->active) {
        $pageLink = clone $uri;
        if ($page->base) {
            $pageLink->setVar('start', $page->base);
        }
        $text = sprintf('<a href="%s">%s</a>', $pageLink, $text);
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

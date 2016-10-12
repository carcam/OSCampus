<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

abstract class OscTeacher
{
    protected static $linkIcons = array(
        'default'  => '<i class="fa"></i>',
        'twitter'  => '<i class="fa fa-twitter"></i>',
        'facebook' => '<i class="fa fa-facebook"></i>',
        'blog'     => '<i class="fa fa-pencil"></i>',
        'email'    => '<i class="fa fa-envelope"></i>',
        'website'  => '<i class="fa fa-globe"></i>'
    );

    /**
     * Generate links to known urls for a teacher
     *
     * @param object $teacher
     *
     * @return string
     */
    public static function links($teacher)
    {
        $html = array();

        if (!empty($teacher->links)) {
            foreach ($teacher->links as $type => $value) {
                if ($type == 'email' && !$value->link && !empty($teacher->email)) {
                    $value->link = $teacher->email;
                }
                if ($link = static::createLink($type, $value->link, $value->show)) {
                    $type    = isset(static::$linkIcons[$type]) ? $type : 'default';
                    $attribs = preg_match('#^https?://#', $link) ? 'target="_blank"' : '';

                    $html[] = '<span class="osc-teacher-' . $type . '">';
                    $html[] = static::$linkIcons[$type];
                    $html[] = JHtml::_(
                        'link',
                        $link,
                        JText::_('COM_OSCAMPUS_TEACHER_LINK_' . $type),
                        $attribs
                    );
                    $html[] = '</span>';
                }
            }

            if ($html) {
                array_unshift($html, '<div class="osc-teacher-links">');
                $html[] = '</div>';
            }
        }

        return join("\n", $html);
    }

    /**
     * Normalize links based on type
     *
     * @param string $type
     * @param string $link
     * @param string $show
     *
     * @return null|string
     */
    protected static function createLink($type, $link, $show)
    {
        if ($link && $show) {
            switch ($type) {
                case 'twitter':
                    $link = 'https://www.twitter.com/' . $link;
                    break;

                case 'facebook':
                    $link = 'https://www.facebook.com/' . $link;
                    break;

                case 'email':
                    $link = 'mailto:' . $link;
                    break;
            }

            return $link;
        }

        return null;
    }
}

<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

abstract class OscTeacher
{
    protected static $linkIcons = array(
        'default' => '<i class="fa"></i>'
    );

    public static function links($teacher, $options = null)
    {
        $html = array();

        if (!empty($teacher->links)) {
            foreach ($teacher->links as $data) {
                if ($data->type == 'email' && !$data->link && !empty($teacher->email)) {
                    $data->link = $teacher->email;
                }
                if ($link = static::createLink($data)) {
                    $type = isset(static::$linkIcons[$data->type]) ? $data->type : 'default';

                    $html[] = '<span class="osc-teacher-' . $data->type . '">';
                    $html[] = static::$linkIcons[$type];
                    $html[] = JHtml::_(
                        'link',
                        $link,
                        JText::_('COM_OSCAMPUS_TEACHER_LINK_' . $data->type),
                        'target="_blank"'
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

    protected static function createLink($data)
    {
        if ($link = $data->link) {
            switch ($data->type) {
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
        }

        if ($link && $data->show) {
            return $link;
        }

        return null;
    }
}

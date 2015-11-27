<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();


class OscampusModelPathway extends OscampusModelAdmin
{
    public function getItem($pk = null)
    {
        $item = parent::getItem($pk);

        if (!$item->id) {
            $item->access    = 1;
            $item->published = 1;

            $defaultImage   = JHtml::_('image', 'com_oscampus/default-course.jpg', null, null, true, true);
            $item->image    = ltrim($defaultImage, '/');

        }
        return $item;
    }
}

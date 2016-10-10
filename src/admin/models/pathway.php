<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

use Oscampus\Pathway;

defined('_JEXEC') or die();


class OscampusModelPathway extends OscampusModelAdmin
{
    public function getItem($pk = null)
    {
        $item = parent::getItem($pk);

        if (!$item->id) {
            $item->access    = 1;
            $item->published = 1;
            $item->image     = Pathway::DEFAULT_IMAGE;
        }

        return $item;
    }

    public function save($data)
    {
        if (empty($data['image'])) {
            $data['image'] = Pathway::DEFAULT_IMAGE;
        }
        return parent::save($data);
    }
}

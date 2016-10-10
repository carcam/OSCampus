<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

class OscampusViewPathways extends OscampusViewAdminList
{
    protected function setup()
    {
        parent::setup();

        $state = $this->getState();

        $enableOrdering = $state->get('list.ordering') == 'pathway.ordering';
        $this->setOrdering('pathway.ordering', 'pathways', $enableOrdering);
    }
}

<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

class OscampusViewTags extends OscampusViewList
{
    protected function setToolbar()
    {
        OscampusToolbarHelper::addNew('tag.add');
        OscampusToolbarHelper::editList('tag.edit');
        OscampusToolbarHelper::deleteList('COM_OSCAMPUS_DELETE_CONFIRM', 'tags.delete');

        parent::setToolbar();
    }
}

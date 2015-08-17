<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

class OscampusViewTeachers extends OscampusViewList
{
    protected function setToolbar()
    {
        OscampusToolbarHelper::addNew('teacher.add');
        OscampusToolbarHelper::editList('teacher.edit');
        OscampusToolbarHelper::deleteList('COM_OSCAMPUS_DELETE_CONFIRM', 'teachers.delete');

        parent::setToolbar();
    }
}

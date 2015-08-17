<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

class OscampusViewTeacher extends OscampusViewForm
{
    protected $item;

    public function display($tpl = null)
    {
        $state = $this->get('State');

        $this->item = $this->get('Item');

        $this->setVariable('form', $this->get('Form'));
        $this->setVariable('item', $this->item);

        parent::display($tpl);
    }

    protected function setToolbar()
    {
        $isNew = ($this->item->id == 0);
        OscampusFactory::getApplication()->input->set('hidemainmenu', true);

        OscampusToolbarHelper::apply('teacher.apply');
        OscampusToolbarHelper::save('teacher.save');

        $alt = $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE';
        OscampusToolbarHelper::cancel('teacher.cancel', $alt);

        parent::setToolbar();
    }
}

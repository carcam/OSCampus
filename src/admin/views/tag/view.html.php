<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

class OscampusViewTag extends OscampusViewForm
{
    protected function setToolbar()
    {
        $isNew = ($this->item->id == 0);
        OscampusFactory::getApplication()->input->set('hidemainmenu', true);

        OscampusToolbarHelper::apply('tag.apply');
        OscampusToolbarHelper::save('tag.save');

        $alt = $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE';
        OscampusToolbarHelper::cancel('tag.cancel', $alt);

        parent::setToolbar();
    }
}

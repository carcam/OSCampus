<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

JFormHelper::loadFieldType('List');

class OscampusFormFieldPathways extends JFormFieldList
{
    protected function getOptions()
    {
        $options = array_map(
            function ($row) {
                $row->selected = false;
                $row->checked  = false;
                $row->disable  = false;
                return $row;
            },
            JHtml::_('osc.options.pathways', false)
        );

        return array_merge(parent::getOptions(), $options);
    }
}

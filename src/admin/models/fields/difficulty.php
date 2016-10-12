<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

JFormHelper::loadFieldType('List');

class OscampusFormFieldDifficulty extends JFormFieldList
{
    protected function getOptions()
    {
        return array_merge(parent::getOptions(), JHtml::_('osc.options.difficulties'));
    }
}

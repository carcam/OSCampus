<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

JFormHelper::loadFieldClass('List');

class OscampusFormFieldModule extends JFormFieldList
{
    protected function getOptions()
    {
        if ($courseId = $this->getCourseId()) {
            $options = JHtml::_('osc.options.modules', $courseId);

            return array_merge(parent::getOptions(), $options);
        }

        return array();
    }

    protected function getCourseId()
    {
        $courseId = null;
        
        if ($courseField = (string)$this->element['coursefield']) {
            $courseId = (int)$this->form->getfield($courseField)->value;

        } elseif ($this->value) {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select('courses_id')
                ->from('#__oscampus_modules')
                ->where('id = ' . (int)$this->value);

            $courseId = $db->setQuery($query)->loadResult();
        }

        return $courseId;
    }
}

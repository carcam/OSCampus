<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

JFormHelper::loadFieldType('Text');

class OscampusFormFieldLinks extends JFormFieldText
{
    protected $commonAttributes = array();

    protected $linkAttributes = array();

    protected $show = null;

    /**
     * @param SimpleXMLElement $element
     * @param mixed            $value
     * @param string           $group
     *
     * @return bool
     */
    public function setup(&$element, $value, $group = null)
    {
        if (!parent::setup($element, $value, $group)) {
            return false;
        }

        // Reformat value to our needs
        if (!empty($this->value)) {
            $value = is_object($this->value) ? $this->value : (object)$this->value;

            $this->show  = isset($value->show) ? $value->show : 0;
            $this->value = isset($value->link) ? $value->link : '';
        }

        // Common additional attributes
        if ($class = (string)$this->element['class']) {
            $this->commonAttributes['class'] = $class;
        }
        if ((string)$this->element['readonly'] == 'true') {
            $this->commonAttributes['readonly'] = 'readonly';
        }
        if ((string)$this->element['disabled'] == 'true') {
            $this->commonAttributes['disabled'] = 'disabled';
        }

        // Attributes meant only for the link field
        if ($size = (int)$this->element['size']) {
            $this->linkAttributes['size'] = $size;
        }
        if ($maxLength = (int)$this->element['maxlength']) {
            $this->linkAttributes['maxlength'] = $maxLength;
        }
        if ((string)$this->element['autocomplete'] == 'off') {
            $this->linkAttributes['autocomplete'] = 'off';
        }
        if ($onchange = (string)$this->element['onchange']) {
            $this->linkAttributes['onchange'] = $onchange;
        }

        return true;
    }

    public function getInput()
    {
        $linkAttribs = array_merge(
            array(
                'type'  => 'text',
                'name'  => $this->name . '[link]',
                'id'    => $this->id,
                'value' => htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8')
            ),
            $this->commonAttributes,
            $this->linkAttributes
        );

        $link = '<input ' . OscampusUtilitiesArray::toString($linkAttribs) . '/>';

        $options = array(
            JHtml::_('select.option', 0, JText::_('COM_OSCAMPUS_OPTION_HIDE')),
            JHtml::_('select.option', 1, JText::_('COM_OSCAMPUS_OPTION_SHOW'))
        );
        $show    = JHtml::_(
            'select.genericlist',
            $options,
            $this->name . '[show]',
            $this->commonAttributes,
            'value',
            'text',
            $this->show,
            $this->id
        );

        return $link . $show;
    }
}
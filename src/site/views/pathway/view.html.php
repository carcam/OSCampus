<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

class OscampusViewPathway extends OscampusViewSite
{
    /**
     * @var array
     */
    protected $items = array();

    /**
     * @var object
     */
    protected $pathway = null;

    public function display($tpl = null)
    {
        /** @var OscampusModelPathway $model */
        $model = $this->getModel();

        $this->items   = $model->getItems();
        $this->pathway = $model->getPathway();

        $pathway = JFactory::getApplication()->getPathway();
        $pathway->addItem($this->pathway->title);

        // Set title and meta-description
        $doc = OscampusFactory::getDocument();
        $metadata = $this->pathway->metadata;

        $title = $metadata->get('title') ?: $this->pathway->title;
        $doc->setTitle($title);

        if ($description = $metadata->get('description')) {
            $doc->setMetaData('description', $description);
        }

        parent::display($tpl);
    }
}

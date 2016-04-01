<?php
/**
 * @package   Oscampus
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2015-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Joomla\Registry\Registry as Registry;

defined('_JEXEC') or die();

abstract class OscampusViewSite extends OscampusView
{
    /**
     * @var Registry
     */
    protected $params = null;

    /**
     * @var int
     */
    protected $step = 1;

    /**
     * Display an incrementing step header. Each subsequent
     * use adds one to the step number
     *
     * @param $text
     *
     * @return string
     */
    protected function stepHeading($text)
    {
        $step = JText::sprintf('COM_OSCAMPUS_HEADING_STEP', $this->step++);

        $html = '<h3><span>' . $step . '</span>' . $text . '</h3>';

        return $html;
    }

    /**
     * @return Registry
     */
    protected function getParams()
    {
        if ($this->params === null) {
            /** @var OscampusModel $model */
            if ($model = $this->getModel()) {
                if (method_exists($model, 'getParams')) {
                    $this->params = $model->getParams();
                }
            }
            if (!($this->params = $this->get('Params'))) {
                $this->params = new Registry();
            }
        }
        return $this->params;
    }

    /**
     * Get the page heading from the menu definition if set
     *
     * @param string $default
     * @param bool   $translate
     *
     * @return string
     */
    protected function getHeading($default = null, $translate = true)
    {
        $params = $this->getParams();

        if ($params->get('show_page_heading')) {
            $heading = $params->get('page_heading');
        } else {
            $heading = $translate ? JText::_($default) : $default;
        }
        return $heading;
    }

    /**
     * Append page class suffix if specified
     *
     * @param string $base
     *
     * @return string
     */
    protected function getPageClass($base = '')
    {
        $suffix = $this->getParams()->get('pageclass_sfx');
        return trim($base . ' ' . $suffix);
    }

    /**
     * Set document title and metadata
     *
     * @param array|object|Registry $metadata
     * @param string                $defaultTitle
     * @param string                $defaultDescription
     */
    protected function setMetadata($metadata, $defaultTitle = null, $defaultDescription = null)
    {
        if (!$metadata instanceof Registry) {
            $metadata = new Registry($metadata);
        }
        $doc = OscampusFactory::getDocument();

        $title = $metadata->get('title') ?: $defaultTitle;
        if ($title) {
            $doc->setTitle($title);
        }

        $description = $metadata->get('description');
        if (!$description && $defaultDescription) {
            $filter = JFilterInput::getInstance();

            $description = $filter->clean($defaultDescription);
            if (strlen($description) > 150) {
                $description = preg_replace('/\s*\w*$/', '', substr($description, 0, 160)) . '...';
            }
        }
        if ($description) {
            $doc->setMetaData('description', $description);
        }
    }

    /**
     * Allow reuse of other view templates by view name
     *
     * @param string $name
     *
     * @return bool
     */
    protected function shareViewTemplates($name)
    {
        $path = OSCAMPUS_SITE . '/views/pathway/tmpl';
        if (is_dir($path)) {
            // Add path to include path
            $this->addTemplatePath($path);

            // but allow local override as needed
            $templatePath              = array_shift($this->_path['template']);
            $this->_path['template'][] = $templatePath;

            return true;
        }

        return false;
    }
}

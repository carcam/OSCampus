<?php
/**
 * @package    Oscampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

class OscampusViewTwig extends OscampusView
{
    /**
     * Variables used in the templates
     *
     * @var array
     */
    protected $variables = array();

    /**
     * The template engine
     *
     * @var
     */
    protected $templatesEngine;

    /**
     * Constructor
     *
     * @param   array  $config  A named configuration array for object construction.<br />
     *                          name: the name (optional) of the view (defaults to the view class name suffix).<br />
     *                          charset: the character set to use for display<br />
     *                          escape: the name (optional) of the function to use for escaping strings<br />
     *                          base_path: the parent path (optional) of the views directory (defaults to the component folder)<br />
     *                          template_plath: the path (optional) of the layout directory (defaults to base_path + /views/ + view name<br />
     *                          helper_path: the path (optional) of the helper files (defaults to base_path + /helpers/)<br />
     *                          layout: the layout (optional) to use to display the view<br />
     *
     * @since   12.2
     */
    public function __construct($config = array())
    {
        parent::__construct($config);

        // Removes not existent directories to avoid break Twig
        foreach ($this->_path['template'] as $index => $path) {
            if (!file_exists($path)) {
                unset($this->_path['template'][$index]);
            }
        }

        $this->_addPath('template', JPATH_COMPONENT . '/layouts');

        $loader  = new Twig_Loader_Filesystem($this->_path['template']);

        $options = array(
            'cache' => JPATH_CACHE
        );

        $this->templatesEngine = new Twig_Environment($loader, $options);

        // Set the default template variables
        $this->variables = array();
    }

    /**
     * Load a template file -- first look in the templates folder for an override
     *
     * @param   string  $tpl  The name of the template source file; automatically searches the template paths and compiles as needed.
     *
     * @return  string  The output of the the template script.
     *
     * @since   12.2
     * @throws  Exception
     */
    public function loadTemplate($tpl = null)
    {
        // Clear prior output
        $this->_output = null;

        $template = JFactory::getApplication()->getTemplate();
        $layout = $this->getLayout();
        $layoutTemplate = $this->getLayoutTemplate();

        // Create the template file name based on the layout
        $file = isset($tpl) ? $layout . '_' . $tpl : $layout;

        // Clean the file name
        $file = preg_replace('/[^A-Z0-9_\.-]/i', '', $file);
        $tpl = isset($tpl) ? preg_replace('/[^A-Z0-9_\.-]/i', '', $tpl) : $tpl;

        // Load the language file for the template
        $lang = JFactory::getLanguage();
        $lang->load('tpl_' . $template, JPATH_BASE, null, false, true)
            || $lang->load('tpl_' . $template, JPATH_THEMES . "/$template", null, false, true);

        // Change the template folder if alternative layout is in different template
        if (isset($layoutTemplate) && $layoutTemplate != '_' && $layoutTemplate != $template)
        {
            $this->_path['template'] = str_replace($template, $layoutTemplate, $this->_path['template']);
        }

        // Load the template script
        jimport('joomla.filesystem.path');
        $filetofind = $this->_createFileName('template', array('name' => $file));
        $this->_template = JPath::find($this->_path['template'], $filetofind);

        // If alternate layout can't be found, fall back to default layout
        if ($this->_template == false)
        {
            $filetofind = $this->_createFileName('', array('name' => 'default' . (isset($tpl) ? '_' . $tpl : $tpl)));
            $this->_template = JPath::find($this->_path['template'], $filetofind);
        }

        if ($this->_template != false)
        {
            // Unset so as not to introduce into template scope
            unset($tpl);
            unset($file);

            // Never allow a 'this' property
            if (isset($this->this))
            {
                unset($this->this);
            }

            // Renders the template
            return $this->templatesEngine->render(basename($this->_template), $this->variables);
        }
        else
        {
            throw new Exception(JText::sprintf('JLIB_APPLICATION_ERROR_LAYOUTFILE_NOT_FOUND', $file), 500);
        }
    }

    /**
     * Create the filename for a resource
     *
     * @param   string  $type   The resource type to create the filename for
     * @param   array   $parts  An associative array of filename information
     *
     * @return  string  The filename
     *
     * @since   12.2
     */
    protected function _createFileName($type, $parts = array())
    {
        switch ($type)
        {
            case 'template':
                $filename = strtolower($parts['name']) . '.' . $this->_layoutExt;
                break;

            default:
                $filename = strtolower($parts['name']) . '.html';
                break;
        }

        return $filename;
    }

    /**
     * Set variables for the template
     *
     * @param  string|array  $name
     * @param  mixed         $value
     *
     * @return     <type>
     */
    protected function setVariable($name, $value = null)
    {
        if (is_array($name)) {
            foreach ($name as $key => $value) {
                $this->variables[$key] = $value;
            }
        } elseif (is_string($name)) {
            $this->variables[$name] = $value;
        }
    }
}

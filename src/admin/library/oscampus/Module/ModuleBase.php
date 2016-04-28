<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

namespace Oscampus\Module;

use JModuleHelper;
use Joomla\Registry\Registry;
use OscampusHelperSite;

defined('_JEXEC') or die();

class ModuleBase
{
    /**
     * @var string
     */
    protected $id = null;

    /**
     * @var string
     */
    protected $name = 'mod_oscampus_module';

    /**
     * @var Registry
     */
    protected $params = null;

    /**
     * @var \JDatabaseDriver
     */
    protected $db = null;

    /**
     * @var int
     */
    protected static $instanceCount = 0;

    public function __construct(Registry $params)
    {
        $this->params = $params;
        $this->db    = \OscampusFactory::getDbo();

        self::$instanceCount++;
        $this->id = $this->name . '_' . self::$instanceCount;
    }

    protected function addScript()
    {
        // To be overloaded by subclasses
    }

    public function output($layout = null)
    {
        $this->addScript();

        $layout = $layout ?: $this->params->get('layout', 'default');
        require JModuleHelper::getLayoutPath($this->name, $layout);
    }
}

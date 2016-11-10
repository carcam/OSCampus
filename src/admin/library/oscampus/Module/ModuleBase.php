<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Oscampus\Module;

use Exception;
use JDatabaseDriver;
use JModuleHelper;
use Joomla\Registry\Registry;
use JText;
use OscampusFactory;

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
    protected $name = null;

    /**
     * @var Registry
     */
    protected $params = null;

    /**
     * @var object
     */
    protected $module = null;

    protected $scope = null;

    /**
     * @var JDatabaseDriver
     */
    protected $db = null;

    protected $cache = null;

    /**
     * @var int[]
     */
    protected static $instanceCount = array();

    /**
     * ModuleBase constructor.
     *
     * @param Registry $params
     * @param object   $module
     *
     * @throws Exception
     */
    public function __construct(Registry $params, $module)
    {
        if (empty($module->module)) {
            throw new Exception(JText::sprintf('COM_OSCAMPUS_ERROR_MODULEBASE', __CLASS__), 500);
        }

        $this->params = $params;
        $this->module = $module;
        $this->name   = $module->module;

        $this->db    = OscampusFactory::getDbo();
        $this->cache = OscampusFactory::getCache($this->name, '');

        if (!isset(static::$instanceCount[$this->name])) {
            static::$instanceCount[$this->name] = 0;
        }
        static::$instanceCount[$this->name]++;
        $this->id = $this->name . '_' . static::$instanceCount[$this->name];
    }

    public function output($layout = null)
    {
        $layout = $layout ?: $this->params->get('layout', 'default');
        require JModuleHelper::getLayoutPath($this->name, $layout);
    }
}

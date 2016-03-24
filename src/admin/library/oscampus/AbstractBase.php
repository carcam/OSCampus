<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

namespace Oscampus;

use JDatabaseDriver;

defined('_JEXEC') or die();

abstract class AbstractBase
{
    /**
     * @var JDatabaseDriver
     */
    protected $dbo = null;

    /**
     * @param JDatabaseDriver $dbo
     */
    public function __construct(JDatabaseDriver $dbo)
    {
        $this->dbo = $dbo;
    }
}

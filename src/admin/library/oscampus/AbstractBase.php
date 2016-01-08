<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

namespace Oscampus;

use JDatabase;

defined('_JEXEC') or die();

abstract class AbstractBase
{
    /**
     * @var JDatabase
     */
    protected $dbo = null;

    /**
     * @param JDatabase $dbo
     */
    public function __construct(JDatabase $dbo)
    {
        $this->dbo = $dbo;
    }
}

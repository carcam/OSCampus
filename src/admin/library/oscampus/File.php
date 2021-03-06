<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Oscampus;

defined('_JEXEC') or die();

class File extends AbstractPrototype
{
    /**
     * @var int
     */
    public $id = null;

    /**
     * @var string
     */
    public $path = null;

    /**
     * @var string
     */
    public $title = null;

    /**
     * @var string
     */
    public $description = null;
}

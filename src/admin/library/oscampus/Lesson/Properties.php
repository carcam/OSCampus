<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

namespace Oscampus\Lesson;

use Oscampus\Object;

defined('_JEXEC') or die();

class Properties extends Object
{
    /**
     * @var int
     */
    public $id = null;

    /**
     * @var int
     */
    public $index = null;

    /**
     * @var int
     */
    public $pathways_id = null;

    /**
     * @var int
     */
    public $courses_id = null;

    /**
     * @var int
     */
    public $modules_id = null;

    /**
     * @var string
     */
    public $title = null;

    /**
     * @var string
     */
    public $alias = null;

    /**
     * @var string
     */
    public $type = null;

    /**
     * @var string
     */
    public $header = null;

    /**
     * @var string
     */
    public $footer = null;

    /**
     * @var int
     */
    public $access = null;

    /**
     * @var int
     */
    public $published = null;

    /**
     * @var string
     */
    public $pathway_title = null;

    /**
     * @var string
     */
    public $course_title = null;

    /**
     * @var string
     */
    public $module_title = null;
}

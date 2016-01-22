<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

namespace Oscampus\Lesson;

use JUser;
use OscampusFactory;

defined('_JEXEC') or die();

class Properties
{
    /**
     * @var int
     */
    public $id = null;

    /**
     * @var int
     */
    public $modules_id = null;

    /**
     * @var int
     */
    public $courses_id = null;

    /**
     * @var int
     */
    public $pathways_id = null;

    /**
     * @var string
     */
    public $type = null;

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
    public $header = null;

    /**
     * @var mixed
     */
    public $content = null;

    /**
     * @var string
     */
    public $footer = null;

    /**
     * @var int
     */
    public $access = null;

    /**
     * @var bool
     */
    public $published = null;

    /**
     * @var bool
     */
    public $authorised = false;

    public function __construct($data = null)
    {
        if ($data) {
            $this->load($data);
        }
    }

    public function __clone()
    {
        $properties = array_keys(get_object_vars($this));
        foreach ($properties as $property) {
            $this->$property = null;
        }
    }

    /**
     * Load properties. We trust that everything needed is being passed
     *
     * @param array|object $data
     * @param JUser        $user
     *
     * @return Properties
     */
    public function load($data, JUser $user = null)
    {
        $user = $user ?: OscampusFactory::getUser();

        if (is_object($data)) {
            $data = get_object_vars($data);
        }
        foreach ($data as $property => $value) {
            if (property_exists($this, $property)) {
                $this->$property = $value;
            }
        }

        $this->authorised = in_array($this->access, $user->getAuthorisedViewLevels());

        return $this;
    }

    /**
     * Determine if this lesson is authorised for the user
     *
     * @param JUser $user
     *
     * @return bool
     */
    public function isAuthorised(JUser $user = null)
    {
        $user = $user ?: OscampusFactory::getUser();

        return in_array($this->access, $user->getAuthorisedViewLevels());
    }
}

<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

class OscampusTableCourses extends OscampusTable
{
    /**
     * @param JDatabaseDriver $db
     */
    public function __construct(&$db)
    {
        parent::__construct('#__oscampus_courses', 'id', $db);
    }

    public function store($updateNulls = false)
    {
        if ($this->alias == '') {
            $this->alias = OscampusApplicationHelper::stringURLSafe($this->title);
        }
        return parent::store($updateNulls);
    }
}

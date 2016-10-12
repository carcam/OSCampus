<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

class OscampusTablePathways extends OscampusTable
{
    /**
     * @param JDatabaseDriver $db
     */
    public function __construct(&$db)
    {
        parent::__construct('#__oscampus_pathways', 'id', $db);
    }

    public function store($updateNulls = false)
    {
        if (empty($this->ordering)) {
            $this->ordering = $this->getNextOrder();
        }

        return parent::store($updateNulls);
    }
}

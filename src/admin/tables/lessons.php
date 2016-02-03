<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

class OscampusTableLessons extends OscampusTable
{
    /**
     * @param JDatabaseDriver $db
     */
    public function __construct(&$db)
    {
        parent::__construct('#__oscampus_lessons', 'id', $db);
    }

    public function check()
    {
        if (!$this->modules_id) {
            $this->setError(JText::_('COM_OSCAMPUS_ERROR_LESSONS_REQUIRED_MODULE'));
            return false;
        }

        if (!$this->alias) {
            $this->setError(JText::_('COM_OSCAMPUS_ERROR_LESSONS_REQUIRED_ALIAS'));
            return false;

        } else {
            $table = OscampusTable::getInstance('Lessons');
            $table->load(
                array(
                    'alias' => $this->alias,
                    'modules_id' => $this->modules_id
                )
            );

            if ($table->id && ($table->id != $this->id)) {
                $this->setError('COM_OSCAMPUS_ERROR_LESSONS_DUPLICATE_ALIAS');
                return false;
            }
        }

        return true;
    }
}

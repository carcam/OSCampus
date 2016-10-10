<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
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

        // Override Joomla global access defaulting
        $defaultAccess = OscampusComponentHelper::getParams()->get('access.lesson');
        $this->access = $defaultAccess;
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
            $db = $this->getDbo();
            $subQuery = $db->getQuery(true)
                ->select('courses_id')
                ->from('#__oscampus_modules')
                ->where('id = ' . (int)$this->modules_id);

            $query = $db->getQuery(true)
                ->select('lesson.id')
                ->from('#__oscampus_modules AS module')
                ->innerJoin('#__oscampus_lessons AS lesson ON lesson.modules_id = module.id')
                ->where(
                    array(
                        "module.courses_id IN ({$subQuery})",
                        'lesson.alias = ' . $db->quote($this->alias),
                        'lesson.id != ' . (int)$this->id
                    )
                );

            $duplicates = $db->setQuery($query)->loadColumn();

            if ($duplicates) {
                $this->setError(JText::sprintf('COM_OSCAMPUS_ERROR_LESSONS_DUPLICATE_ALIAS', $this->alias));
                return false;
            }
        }

        return true;
    }
}

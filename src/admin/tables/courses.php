<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
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

    public function check()
    {
        if (!$this->alias) {
            $this->setError(JText::_('COM_OSCAMPUS_ERROR_COURSES_REQUIRED_ALIAS'));
            return false;
        } else {
            $db = $this->getDbo();

            $query = $db->getQuery(true)
                ->select('course.id')
                ->from('#__oscampus_courses AS course')
                ->where(
                    array(
                        'course.alias = ' . $db->quote($this->alias),
                        'course.id != ' . (int)$this->id
                    )
                );

            $duplicates = $db->setQuery($query)->loadColumn();
            if ($duplicates) {
                $this->setError(JText::sprintf('COM_OSCAMPUS_ERROR_COURSES_DUPLICATE_ALIAS', $this->alias));
                return false;
            }

        }

        return true;
    }

    /**
     * Individual nudges require special handling due to m:m courses/pathways relationship
     *
     * @param int   $delta
     * @param array $where
     *
     * @return bool
     * @throws Exception
     */
    public function move($delta, $where = array())
    {
        if (empty($delta)) {
            return true;
        }

        if (empty($where)) {
            $app       = OscampusFactory::getApplication();
            $pathwayId = $app->input->getInt('filter_pathway');
            if (!$pathwayId) {
                throw new UnexpectedValueException(JText::_('COM_OSCAMPUS_ERROR_COURSE_REORDER_PATHWAY'));
            }

            $where = array('pathways_id = ' . $pathwayId);
        }

        $db    = $this->getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__oscampus_courses_pathways')
            ->where($where)
            ->order('ordering ASC');

        $junctions = $db->setQuery($query)->loadObjectList();
        if ($error = $db->getErrorMsg()) {
            throw new RuntimeException($error);
        }

        $pathwayId = $junctions[0]->pathways_id;

        $sql = 'UPDATE #__oscampus_courses_pathways SET ordering = %s WHERE courses_id = %s AND pathways_id = ' . $pathwayId;
        foreach ($junctions as $index => $junction) {
            if ($delta < 0 && !empty($junctions[$index + 1]) && $junctions[$index + 1]->courses_id == $this->id) {
                // Move item up
                $db->setQuery(sprintf($sql, $index + 2, $junction->courses_id))->execute();
                $db->setQuery(sprintf($sql, $index + 1, $this->id))->execute();

            } elseif ($delta > 0 && !empty($junctions[$index - 1]) && $junctions[$index - 1]->courses_id == $this->id) {
                // Move item down
                $db->setQuery(sprintf($sql, $index, $junction->courses_id))->execute();
                $db->setQuery(sprintf($sql, $index + 1, $this->id))->execute();

            } elseif ($junction->courses_id != $this->id) {
                $db->setQuery(sprintf($sql, $index + 1, $junction->courses_id))->execute();
            }
            if ($error = $db->getErrorMsg()) {
                throw new Exception($error);
            }
        }

        return true;
    }
}

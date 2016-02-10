<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();


class OscampusModelCourse extends OscampusModelAdmin
{
    /**
     * @param int $pk
     *
     * @return object|false
     */
    public function getItem($pk = null)
    {
        $item = parent::getItem($pk);

        if (!$item->id) {
            $item->setProperties(
                array(
                    'released'  => date('Y-m-d'),
                    'access'    => 1,
                    'published' => 1,
                    'image'     => \Oscampus\Course::DEFAULT_IMAGE,
                    'pathways'  => array(),
                    'tags'      => array()
                )
            );

        } else {
            $item->pathways = $this->getPathways($item->id);
            $item->tags     = $this->getTags($item->id);
        }

        return $item;
    }

    /**
     * @param null $courseId
     *
     * @return array|mixed
     * @throws Exception
     */
    public function getPathways($courseId = null)
    {
        if ($courseId = (int)($courseId ?: $this->getState($this->getName() . '.id'))) {
            $db = $this->getDbo();

            $query = $db->getQuery(true)
                ->select('pathways_id')
                ->from('#__oscampus_courses_pathways')
                ->where('courses_id = ' . $courseId);

            $pathways = $db->setQuery($query)->loadColumn();
            if ($error = $db->getErrorMsg()) {
                $this->setError($error);
            } else {
                return $pathways;
            }
        }

        return array();
    }

    public function getTags($courseId = null)
    {
        if ($courseId = (int)($courseId ?: $this->getState($this->getName() . '.id'))) {
            $db    = $this->getDbo();
            $query = $db->getQuery(true)
                ->select('tags_id')
                ->from('#__oscampus_courses_tags')
                ->where('courses_id = ' . $courseId);

            $tags = $db->setQuery($query)->loadColumn();
            if ($error = $db->getErrorMsg()) {
                $this->setError($error);
            } else {
                return $tags;
            }
        }

        return array();
    }

    /**
     * Due to m:m relationship to pathways, we do a special reordering here
     *
     * @param array $pks
     * @param array $order
     *
     * @return bool
     * @throws Exception
     */
    public function saveorder($pks = null, $order = null)
    {
        $app = OscampusFactory::getApplication();
        if ($pathwayId = $app->input->getInt('filter_pathway')) {
            $db  = $this->getDbo();
            $sql = 'UPDATE #__oscampus_courses_pathways SET ordering = %s WHERE courses_id = %s AND pathways_id = ' . $pathwayId;
            foreach ($pks as $index => $courseId) {
                $db->setQuery(sprintf($sql, $order[$index], $courseId))->execute();
                if ($error = $db->getErrorMsg()) {
                    $this->setError($error);
                    return false;
                }
            }

            return true;
        }

        $this->setError(JText::_('COM_OSCAMPUS_ERROR_COURSE_REORDER_PATHWAY'));
        return false;
    }

    protected function getReorderConditions($table)
    {
        $app = OscampusFactory::getApplication();

        if ($pathwayId = $app->input->getInt('filter_pathway')) {
            return array('pathways_id = ' . $pathwayId);
        }

        return array();
    }

    public function save($data)
    {
        if (parent::save($data)) {
            // Handle additional update tasks
            $courseId = (int)$this->getState($this->getName() . '.id');
            $pathways = empty($data['pathways']) ? array() : $data['pathways'];
            $tags     = empty($data['tags']) ? array() : $data['tags'];
            $ordering = empty($data['lessons']) ? array() : $data['lessons'];

            return $this->updatePathways($courseId, $pathways)
            && $this->updateJunctionTable(
                '#__oscampus_courses_tags.courses_id',
                $courseId,
                'tags_id',
                $tags
            )
            && $this->setLessonOrder($ordering);
        }

        return false;
    }

    /**
     * Special handling for pathways junction due to ordering being stored there
     *
     * @param int   $courseId
     * @param int[] $pathways
     *
     * @return bool
     */
    protected function updatePathways($courseId, array $pathways)
    {
        $db = $this->getDbo();

        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__oscampus_courses_pathways')
            ->where('courses_id = ' . (int)$courseId);

        $oldPathways = $db->setQuery($query)->loadObjectList('pathways_id');
        $newPathways = array_flip($pathways);

        // Remove from unselected pathways
        if ($remove = array_diff_key($oldPathways, $newPathways)) {
            $query = $db->getQuery(true)
                ->delete('#__oscampus_courses_pathways')
                ->where(
                    array(
                        'courses_id = ' . (int)$courseId,
                        'pathways_id IN (' . join(',', array_keys($remove)) . ')'
                    )
                );
            $db->setQuery($query)->execute();
            if ($error = $db->getErrorMsg()) {
                $this->setError($error);
                return false;
            }
        }

        // Add to new pathways at bottom of list
        if ($add = array_diff_key($newPathways, $oldPathways)) {
            $query = $db->getQuery(true)
                ->select('pathways_id, max(ordering) AS lastOrder')
                ->from('#__oscampus_courses_pathways')
                ->where('pathways_id IN (' . join(',', array_keys($add)) . ')')
                ->group('pathways_id');

            $ordering = $db->setQuery($query)->loadObjectList('pathways_id');
            foreach ($add as $pid => $null) {
                $add[$pid] = join(',', array((int)$courseId, (int)$pid, (int)$ordering[$pid]->lastOrder + 1));
            }

            $query = $db->getQuery(true)
                ->insert('#__oscampus_courses_pathways')
                ->columns(array('courses_id', 'pathways_id', 'ordering'))
                ->values($add);

            $db->setQuery($query)->execute();
            if ($error = $db->getErrorMsg()) {
                $this->setError($error);
                return false;
            }
        }

        return true;
    }

    /**
     * Do a forced/manual ordering update for all modules and lessons.
     * We are trusting that all ids are appropriate to this course.
     *
     * @param array $ordering
     *
     * @return bool
     */
    protected function setLessonOrder(array $ordering)
    {
        $db = $this->getDbo();

        $moduleOrder = 1;
        foreach ($ordering as $moduleId => $lessons) {
            $setModule = (object)array(
                'id'       => $moduleId,
                'ordering' => $moduleOrder++
            );
            $db->updateObject('#__oscampus_modules', $setModule, 'id');
            if ($error = $db->getErrorMsg()) {
                $this->setError($error);
                return false;
            }

            foreach ($lessons as $lessonOrder => $lessonId) {
                $set = (object)array(
                    'id'       => $lessonId,
                    'ordering' => $lessonOrder + 1
                );
                $db->updateObject('#__oscampus_lessons', $set, 'id');
                if ($error = $db->getErrorMsg()) {
                    $this->setError($error);
                    return false;
                }
            }
        }

        return true;
    }
}

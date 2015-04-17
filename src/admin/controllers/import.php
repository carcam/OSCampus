<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

class OscampusControllerImport extends OscampusControllerBase
{
    protected $classMap = array(
        'id'                     => null,
        'catid'                  => null, // Moving to pathways
        'name'                   => 'title',
        'alias'                  => 'alias',
        'description'            => null,
        'introtext'              => null,
        'image'                  => 'image',
        'emails'                 => null,
        'published'              => 'published',
        'startpublish'           => 'publish_up',
        'endpublish'             => 'publish_down',
        'metatitle'              => null,
        'metakwd'                => 'metakey',
        'metadesc'               => 'metadesc',
        'ordering'               => 'ordering',
        'pre_req'                => null,
        'pre_req_books'          => null,
        'reqmts'                 => null,
        'author'                 => null, // instructors_id
        'level'                  => null, // Do we want to specify levels for classes?
        'priceformat'            => null,
        'skip_module'            => null,
        'chb_free_courses'       => null,
        'step_access_courses'    => null,
        'selected_course'        => null,
        'course_type'            => null,
        'lesson_release'         => null,
        'lessons_show'           => null,
        'start_release'          => null,
        'id_final_exam'          => null,
        'certificate_term'       => null,
        'hasquiz'                => null,
        'updated'                => null,
        'certificate_course_msg' => null,
        'avg_certc'              => null,
        'course_length'          => null,
        'cms_type'               => null
    );

    public function import()
    {
        echo '<p><a href="index.php?option=com_oscampus">Back to main  screen</a></p>';

        $classes = $this->copyTable('#__guru_program', '#__oscampus_classes', $this->classMap);

        foreach ($classes as $class) {
            echo '<br/>' . $class->oldId . ' => ' . $class->id . ': ' . $class->title;
        }

    }

    protected function copyTable($from, $to, array $map)
    {
        $dbGuru = $this->getGuruDbo();
        $dbCampus = JFactory::getDbo();

        $dbCampus->setQuery('Delete From ' . $dbCampus->quoteName($to))->execute();
        $dbCampus->setQuery('Alter Table ' . $dbCampus->quoteName($to) . ' AUTO_INCREMENT=1')->execute();

        $guruQuery = $dbGuru->getQuery(true)
            ->select('*')
            ->from($from);

        $fields = array_filter($map);
        $guruData = $dbGuru->setQuery($guruQuery)->loadAssocList();
        $campusData = array();
        foreach ($guruData as $row) {
            $converted = (object)array();
            foreach ($fields as $guruField => $campusField) {
                $converted->$campusField = $row[$guruField];
            }
            $dbCampus->insertObject($to, $converted);
            if ($newId = $dbCampus->insertid()) {
                $converted->oldId = $row['id'];
                $converted->id = $newId;
            }
            $campusData[] = $converted;
        }

        return $campusData;
    }

    /**
     * @return JDatabase
     */
    protected function getGuruDbo()
    {
        $conf = JFactory::getConfig();

        $options = array(
            'driver'   => $conf->get('dbtype'),
            'host'     => $conf->get('host'),
            'user'     => $conf->get('user'),
            'password' => $conf->get('password'),
            'database' => 'guru',
            'prefix'   => 'hfps_'
        );

        $dbo = JDatabase::getInstance($options);
        return $dbo;
    }
}

<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

class OscampusModelCertificate extends OscampusModelSite
{
    public function getItem()
    {
        if ($id = $this->getState('certificate.id')) {
            $db = $this->getDbo();

            $query = $db->getQuery(true)
                ->select(
                    array(
                        'certificate.*',
                        'student.username',
                        'student.name',
                        'course.title AS course_title',
                        'teacher_info.name AS teacher_name'
                    )
                )
                ->from('#__oscampus_certificates AS certificate')
                ->innerJoin('#__oscampus_courses AS course ON course.id = certificate.courses_id')
                ->innerJoin('#__oscampus_teachers AS teacher ON teacher.id = course.teachers_id')
                ->innerJoin('#__users AS teacher_info ON teacher_info.id = teacher.users_id')
                ->innerJoin('#__users AS student ON student.id = certificate.users_id')
                ->where('certificate.id = ' . $id);

            $certificate = $db->setQuery($query)->loadObject();

            if ($certificate->id) {
                $user = OscampusFactory::getUser();
                if ($certificate->users_id == $user->id || $user->authorise('core.manage')) {
                    $certificate->date = new DateTime($certificate->date);
                    return $certificate;
                }

                throw new Exception('You don\'t have access to that certificate');
            }
        }

        throw new Exception('No certificate requested');
    }

    protected function populateState()
    {
        $app = OscampusFactory::getApplication();

        $id = $app->input->getInt('id');
        $this->setState('certificate.id', $id);
    }
}

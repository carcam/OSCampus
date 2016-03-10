<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

JLoader::import('courses', __DIR__);

class OscampusModelPathway extends OscampusModelCourses
{
    protected function getListQuery()
    {
        $query = $this->getBaseQuery()
            ->group('course.id');

        return $query;
    }
}

<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

class OscampusViewLessons extends OscampusViewAdminList
{
    protected function setup()
    {
        parent::setup();

        $ordering = $this->getState()->get('list.ordering');

        $this->setOrdering('lesson.ordering', 'lessons', $ordering == 'lesson.ordering');

        // Setup and render the batch form
        OscampusFactory::getDocument()->addStyleDeclaration('.modal-body { height: 250px; }');
        $batchBody   = $this->loadTemplate('batch_body');
        $batchFooter = $this->loadTemplate('batch_footer');
        $this->setBatchForm($batchBody, $batchFooter);
    }

    public function getSortGroupId($item)
    {
        return $item->modules_id;
    }
}

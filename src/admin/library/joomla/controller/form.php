<?php
/**
 * @package   com_oscampus
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

jimport('joomla.application.component.controllerform');

abstract class OscampusControllerForm extends JControllerForm
{
    /**
     * Standard form token check and redirect
     *
     * @return void
     */
    protected function checkToken()
    {
        if (!JSession::checkToken()) {
            $home = OscampusFactory::getApplication()->getMenu()->getDefault();

            OscampusFactory::getApplication()->redirect(
                JRoute::_('index.php?Itemid=' . $home->id),
                JText::_('JINVALID_TOKEN'),
                'error'
            );
        }
    }

    public function batch($model = null)
    {
        $this->checkToken();

        $app       = OscampusFactory::getApplication();
        $inflector = \Oscampus\String\Inflector::getInstance();
        $view      = $app->input->getCmd('view', $this->default_view);

        if ($inflector->isPlural($view)) {
            $modelName = $inflector->toSingular($view);

            /** @var OscampusModelAdmin $model */
            $model = $this->getModel($modelName, '', array());

            $linkQuery = http_build_query(
                array(
                    'option' => $app->input->getCmd('option'),
                    'view'   => $view
                )
            );
            $this->setRedirect(JRoute::_('index.php?' . $linkQuery . $this->getRedirectToListAppend(), false));
            return parent::batch($model);
        }

        throw new Exception(JText::_('COM_OSCAMPUS_ERROR_BATCH_METHOD'), 500);
    }
}

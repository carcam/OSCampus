<?php
/**
 * @package   Oscampus
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2015-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

class OscampusControllerBase extends JControllerLegacy
{
    public function display($cachable = false, $urlparams = array())
    {
        if (OscampusFactory::getApplication()->isAdmin()) {
            $app       = OscampusFactory::getApplication();
            $inflector = \Oscampus\String\Inflector::getInstance();
            $view      = $app->input->getCmd('view', $this->default_view);
            $layout    = $app->input->getCmd('layout', '');
            $id        = $app->input->getInt('id');

            // Check for edit form.
            if ($inflector->isSingular($view)
                && $layout == 'edit'
                && !$this->checkEditId('com_oscampus.edit.' . $view, $id)
            ) {
                // Somehow the person just went to the form - we don't allow that.
                $this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
                $this->setMessage($this->getError(), 'error');

                $listView = $inflector->toPlural($view);
                $this->setRedirect(JRoute::_('index.php?option=com_oscampus&view=' . $listView, false));

                return false;
            }
        }

        parent::display();
        return $this;
    }

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

    /**
     * Standard return to calling url. In order:
     *    - Looks for base64 encoded 'return' URL variable
     *    - Uses current 'Itemid' URL variable
     *    - Uses current 'option', 'view', 'layout' URL variables
     *    - Goes to site default page
     *
     * @param array|string $message The message to queue up
     * @param string       $type    message|notice|error
     * @param string       $return  (optional) base64 encoded url for redirect
     *
     * @return void
     */
    protected function callerReturn($message = null, $type = null, $return = null)
    {
        $app = OscampusFactory::getApplication();

        $url = $return ?: $app->input->getBase64('return');
        if ($url) {
            $url = base64_decode($url);

        } else {
            $url = new JURI('index.php');

            if ($itemid = $app->input->getInt('Itemid')) {
                $menu = $app->getMenu()->getItem($itemid);

                $url->setVar('Itemid', $itemid);

            } elseif ($option = $app->input->getCmd('option')) {
                $url->setVar('option', $option);
            }

            if ($view = $app->input->getCmd('view')) {
                $url->setVar('view', $view);
                if ($layout = $app->input->getCmd('layout')) {
                    $url->setVar('layout', $layout);
                }
            }
        }

        if (is_array($message)) {
            $message = join('<br/>', $message);
        }
        $this->setRedirect(JRoute::_((string)$url), $message, $type);
    }
}

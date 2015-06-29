<table width="100%" class="ost-video-actions-header">
    <tr>
        <td align="left" class="guru-classoverview-link ost-guru-lessontitle-left">
            <?php echo "<h1 id=\"step_title\">" . $step->name . "</h1>"; ?>
            <?php
            $authorizeVideoFeatures = $user->authorise('video.control', 'com_oswistia');
            if ($authorizeVideoFeatures) :
                require_once JPATH_SITE . '/components/com_guru/helpers/mobile_detect.php';

                $detect = new Mobile_Detect;
                if (!$detect->isMobile()) :
                    $authorizeDownload = $user->authorise('video.download', 'com_oswistia');

                    if ($authorizeDownload) {
                        $downloadURL      = JRoute::_(
                            'index.php?option=com_oswistia&task=media.download&format=raw&id=%s'
                        );
                        $downloadLimitURL = JRoute::_(
                            'index.php?option=com_oswistia&task=media.get_user_download_limit&format=raw'
                        );
                    } else {
                        $downloadURL      = JRoute::_('/plans');
                        $downloadLimitURL = '';
                    }

                    $authorizeDownloadStr = $authorizeDownload ? 'true' : 'false';
                    ?>
                    <script>
                        jQuery(function() {
                            wistiaEmbed.ready(function() {
                                addWistiaExtraControls(
                                    <?php echo $authorizeDownloadStr; ?>,
                                    '<?php echo $downloadURL; ?>',
                                    '<?php echo JHtml::_("form.token"); ?>',
                                    '<?php echo $downloadLimitURL; ?>'
                                );

                                fixVideoSizeProportion();
                            });
                        });
                    </script>
                <?php
                endif;
            endif;
            ?>
        </td>
        <td align="right" class="ost-guru-lessontitle-right">
            <div id="course-navigation" class="uk-button-group">
                <a class="uk-button" onclick="javascript:closeBox();"
                   href="<?php echo JRoute::_('index.php?option=com_guru&view=guruPrograms&task=view&cid=' . $step->pid . '&Itemid=' . $Itemid); ?>">
                    <i class="uk-icon-bars"></i>
                    <span
                        class="uk-hidden-small uk-hidden-medium"> <?php echo JText::_("GURU_COURSE_HOME_PAGE"); ?></span>
                </a>
                <a id="nextbut" class="modal uk-button" rel="{handler: 'iframe', size: {x: 400, y: 400}}"
                   href="<?php echo JRoute::_("index.php?option=com_guru&view=guruEditplans&course_id=" . intval($catid) . "&tmpl=component"); ?>">
                    <span class="uk-hidden-small uk-hidden-medium"><?php echo JText::_("GURU_NEXT_LESSON"); ?> </span>
                    <i class="uk-icon-chevron-right"></i>
                </a>
                <a id="nextbut" class="uk-button" id="nextbut"
                   href="<?php echo JRoute::_("index.php?option=com_guru&view=guruTasks&catid=" . intval(JRequest::getVar("catid")) . "&task=view&module=" . $step->next_module . "&cid=" . $step->nexts . $tmpl); ?>">
                    <span class="uk-hidden-small uk-hidden-medium"><?php echo JText::_("GURU_NEXT_LESSON"); ?> </span>
                    <i class="uk-icon-chevron-right"></i>
                </a>
            </div>
        </td>
    </tr>
</table>

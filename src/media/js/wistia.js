(function($) {
    $.Oscampus = $.extend({}, $.Oscampus, {
        wistia: {
            options: {
                download: {
                    authorised: false,
                    formToken : null
                }
            },

            fixVolumeBug: function() {
                wistiaEmbed.volume(parseFloat(wistiaEmbed.options.volume));

                wistiaEmbed.bind('volumechange', function(event) {
                    $.Oscampus.ajax({
                        data   : {
                            task : 'wistia.setVolumeLevel',
                            level: wistiaEmbed.volume()
                        },
                        success: function(level) {
                            // ignore response
                        }
                    });
                })
            },

            addExtraControls: function(options) {
                options = $.extend(this.options, options);

                // Add the container for the buttons
                var container = $('<div>')
                    .attr('id', wistiaEmbed.uuid + '_buttons_container')
                    .addClass('osc-lesson-options osc-btn-group');
                $(wistiaEmbed.grid.top_inside).append(container);

                var updateIcon = function(waiting) {
                    if (waiting) {
                        this.icon.removeClass(this.data('icon-disabled'));
                        this.icon.removeClass(this.data('icon-enabled'));
                        this.icon.addClass('fa-spinner fa-spin');
                    } else {
                        this.icon.removeClass('fa-spinner fa-spin');

                        if (wistiaEmbed.options[this.data('state-option')]) {
                            this.icon.removeClass(this.data('icon-disabled'));
                            this.icon.addClass(this.data('icon-enabled'));
                        } else {
                            this.icon.addClass(this.data('icon-disabled'));
                            this.icon.removeClass(this.data('icon-enabled'));
                        }
                    }
                };

                /*********** BEGIN DOWNLOAD ***************/
                var buttonDownload = $('<div>')
                    .attr('id', wistiaEmbed.uuid + '_download_button')
                    .addClass('download osc-btn')
                    .attr('title', 'Download')
                    .text('Download')
                    .prepend($('<i class="fa fa-cloud-download">'))
                    .on('click', function(event) {
                        if (options.download.authorised) {
                            wistiaEmbed.pause();

                            // Get the download limit information
                            $.Oscampus.ajax({
                                data   : {
                                    task: 'wistia.downloadLimit'
                                },
                                success: function(data) {
                                    if (data.authorised) {
                                        var formId = 'formDownload-' + wistiaEmbed.hashedId();
                                        var form = $("#" + formId);
                                        if (form.length == 0) {
                                            var query = {
                                                option: 'com_oscampus',
                                                task  : 'wistia.download',
                                                id    : wistiaEmbed.hashedId(),
                                                format: 'raw'
                                            };

                                            form = $('<form>').attr({
                                                id    : formId,
                                                method: 'POST',
                                                action: 'index.php?' + $.param(query)
                                            });

                                            if (options.download.formToken) {
                                                var tokenField = $('<input type="hidden">')
                                                    .attr('name', options.download.formToken)
                                                    .val(1);
                                                form.append(tokenField);
                                            }

                                            form.css('osc-visible', 'osc-hidden');
                                            $('body').append(form);
                                        }
                                        form.submit();

                                    } else {
                                        $.Oscampus.wistia.openOverlay(container.parent, '<div style="padding-left: 25%; padding-right: 25%;">' + data.error + '</div>' +
                                            '</a><a href="#" id="' + wistiaEmbed.uuid + '_resume_skip" class="skip">' +
                                            '  <span id="' + wistiaEmbed.uuid + '_resume_skip_arrow">&nbsp;</span>' +
                                            '  Skip to where you left off' +
                                            '</a>'
                                        )

//                                        $('#' + wistiaEmbed.uuid + '_resume_skip').click(function() {
//                                            overlay.fadeOut(200, function() {
//                                                overlay.remove();
//                                                wistiaEmbed.play();
//                                            });
//                                        });

                                    }
                                }
                            });

                        } else {
                            wistiaEmbed.pause();

                            var overlay = $.Oscampus.wistia.openOverlay(wistiaEmbed.uuid, '<div>Become a Pro Member<br>to download this video!</div>' +
                                '<a href="#" class="subscribe_link">' +
                                '  <span class="subscribe_icon">&nbsp;</span>' +
                                '  Subscribe as Pro to download' +
                                '</a><a href="#" class="resume_skip">' +
                                '  <span class="resume_skip_arrow">&nbsp;</span>' +
                                '  Skip to where you left off' +
                                '</a>'
                            );
                            container.append(overlay);

                        }
                    });

                if (!options.download.authorised) {
                    buttonDownload.addClass('osc-off');
                }
                container.append(buttonDownload);
                /*********** END DOWNLOAD ***************/

                /*********** BEGIN AUTOPLAY ***************/
                var buttonAutoplay = $('<div>');
                buttonAutoplay.icon = $('<i class="fa">');
                buttonAutoplay
                    .attr('id', wistiaEmbed.uuid + '_autoplay_button')
                    .addClass('osc-btn autoplay')
                    .attr('title', 'Autoplay')
                    .text('Autoplay')
                    .prepend(buttonAutoplay.icon)
                    .data('icon-disabled', 'fa-square-o')
                    .data('icon-enabled', 'fa-check-square')
                    .data('state-option', 'autoPlay')
                    .on('click', function(event) {
                        $.Oscampus.ajax({
                            data   : {
                                task: 'wistia.toggleAutoPlayState'
                            },
                            beforeSend: function() {
                                buttonAutoplay.updateIcon(true);
                            },
                            success: function(state) {
                                wistiaEmbed.options.autoPlay = state;
                                wistiaEmbed.params.autoplay = state;

                                if (state) {
                                    $(wistiaEmbed).trigger('autoplayenabled');
                                } else {
                                    $(wistiaEmbed).trigger('autoplaydisabled');
                                }

                                buttonAutoplay.updateIcon();
                            }
                        })
                    });

                buttonAutoplay.updateIcon = updateIcon;
                container.append(buttonAutoplay);

                buttonAutoplay.updateIcon();
                /*********** END AUTOPLAY ***************/

                /*********** BEGIN FOCUS ***************/
                var buttonFocus = $('<div>');
                buttonFocus.icon = $('<i class="fa">');
                buttonFocus
                    .attr('id', wistiaEmbed.uuid + '_focus_button')
                    .addClass('osc-btn focus')
                    .attr('title', 'Focus')
                    .text('Focus')
                    .prepend(buttonFocus.icon)
                    .data('icon-disabled', 'fa-square-o')
                    .data('icon-enabled', 'fa-check-square')
                    .data('state-option', 'focus')
                    .on('click', function(event) {
                        $.Oscampus.ajax({
                            data   : {
                                task: 'wistia.toggleFocusState'
                            },
                            beforeSend: function() {
                                buttonFocus.updateIcon(true);
                            },
                            success: function(state) {
                                wistiaEmbed.options.focus = state;
                                wistiaEmbed.params.focus = state;

                                if (state) {
                                    wistiaEmbed.plugin['dimthelights'].dim();
                                    $(wistiaEmbed).trigger('focusenabled');
                                } else {
                                    wistiaEmbed.plugin['dimthelights'].undim();
                                    $(wistiaEmbed).trigger('focusdisabled');
                                }

                                buttonFocus.updateIcon();
                            }
                        });
                    });
                buttonFocus.updateIcon = updateIcon;
                container.append(buttonFocus);

                buttonFocus.updateIcon();

                // Fix the focus on play
                wistiaEmbed.bind('play', function(event) {
                    if (wistiaEmbed.options.focus) {
                        // Uses interval to make sure the plugin is loaded before call it
                        var interval = setInterval(function() {
                            if (typeof wistiaEmbed.plugin['dimthelights'] != 'undefined') {
                                wistiaEmbed.plugin['dimthelights'].dim();
                                clearInterval(interval);
                                interval = null;
                            }
                        }, 500);
                    }
                });
                /*********** END FOCUS ***************/

                // Autoplay move to the next lesson and turn focus off
                wistiaEmbed.bind('end', function(event) {
                    if (wistiaEmbed.options.autoPlay) {
                        $('#nextbut')[0].click();
                    }

                    wistiaEmbed.plugin['dimthelights'].undim();
                });
            },

            moveNavigationButtons: function() {
                $(wistiaEmbed.grid.top_inside).append($('#course-navigation'));

                // Events
                var gridMain = $(wistiaEmbed.grid.main);

                var hideWistiaButtons = function() {
                    var buttons = $('.osc-btn, #course-navigation');

                    buttons.removeClass('osc-visible');
                    buttons.addClass('osc-hidden');
                };

                var showWistiaButtons = function() {
                    var buttons = $('.osc-btn, #course-navigation');

                    buttons.addClass('osc-visible');
                    buttons.removeClass('osc-hidden');
                };

                gridMain.mouseenter(showWistiaButtons);
                gridMain.mousemove(showWistiaButtons);
                gridMain.mouseleave(hideWistiaButtons);
                $('.osc-btn').hover(showWistiaButtons);

                wistiaEmbed.bind('play', hideWistiaButtons);
                wistiaEmbed.bind('pause', hideWistiaButtons);
            },

            getOverlay: function(id, text) {
                var overlay = $('<div>')
                    .attr('id', 'overlay_' + id)
                    .addClass('osc-wistia-overlay');

                var wrapper = $('<div>').addClass('wrapper');
                $(overlay).append(wrapper);

                wrapper.html(text);

                console.log($('#overlay_'+id+' .subcribe_'))

                $('#subscribe').click(function() {
                    //window.location = downloadURL;
                });

                $('#resume_skip').click(function() {
                    overlay.fadeOut(200, function() {
                        overlay.remove();
                        wistiaEmbed.play();
                    });
                });

                var resize = function() {
                    overlay.css('height', $(wistiaEmbed.grid.main).height());
                    overlay.css('width', $(wistiaEmbed.grid.main).width());
                    wrapper.css('top', Math.max(0, (overlay.height() - wrapper.height()) / 2) + 'px');
                };

                resize();
                wistiaEmbed.bind('widthchange', resize);
                wistiaEmbed.bind('heightchange', resize);

                overlay.addClass('osc-visible');
            }
        }
    });
})(jQuery);

(function($) {
    $.extend($.fn, {
        spinnerIcon: function(waiting) {
            var data = $(this).data();

            if (data.icon) {
                var disabled = data.disabled || 'fa-square-o',
                    enabled  = data.enabled || 'fa-check-square',
                    option   = data.option || 'generic';

                if (waiting) {
                    data.icon
                        .removeClass(disabled)
                        .removeClass(enabled)
                        .addClass('fa-spinner fa-spin');
                } else {
                    data.icon.removeClass('fa-spinner fa-spin');

                    if (wistiaEmbed.options[option]) {
                        data.icon
                            .removeClass(disabled)
                            .addClass(enabled);
                    } else {
                        data.icon
                            .addClass(disabled)
                            .removeClass(enabled);
                    }
                }
            }
        }
    });

    $.Oscampus = $.extend({}, $.Oscampus, {
        wistia: {
            options: {
                extras: false,
                download: {
                    authorised: false,
                    formToken : null
                }
            },

            init: function(options) {
                options = $.extend(this.options, options);

                this.moveNavigationButtons();
                this.saveVolumeChange();

                if (options.extras) {
                    this.addExtraControls(options);
                }
            },

            /**
             * Save volume changes in session as user navigates through videos
             */
            saveVolumeChange: function() {
                if (wistiaEmbed) {
                    wistiaEmbed.volume(parseFloat(wistiaEmbed.options.volume));

                    var saveVolume;

                    wistiaEmbed.bind('volumechange', function(event) {
                        if (saveVolume) {
                            clearTimeout(saveVolume);
                        }
                        saveVolume = setTimeout(function() {
                            $.Oscampus.ajax({
                                data   : {
                                    task : 'wistia.setVolumeLevel',
                                    level: wistiaEmbed.volume()
                                },
                                success: function(level) {
                                    // ignore response
                                }
                            });
                        }, 750);

                    })
                }
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

            addExtraControls: function(options) {
                // Add the container for the buttons
                var container = $('<div>')
                    .attr('id', wistiaEmbed.uuid + '_buttons_container')
                    .addClass('osc-lesson-options osc-btn-group');
                $(wistiaEmbed.grid.top_inside).append(container);

                this.buttonAutoplay(container);
                this.buttonFocus(container);
            },

            /**
             * Add autoplay button to the container
             *
             * @param container
             */
            buttonAutoplay: function(container) {
                var button = $('<div>'),
                    icon   = $('<i class="fa">');

                button
                    .data({
                        icon  : icon,
                        option: 'autoPlay'
                    })
                    .attr('id', wistiaEmbed.uuid + '_autoplay_button')
                    .addClass('osc-btn autoplay')
                    .attr('title', 'Autoplay')
                    .text('Autoplay')
                    .prepend(icon)
                    .on('click', function(event) {
                        $(this).spinnerIcon(true);
                        $.Oscampus.ajax({
                            context: this,
                            data   : {
                                task: 'wistia.toggleAutoPlayState'
                            },
                            success: function(state) {
                                wistiaEmbed.options.autoPlay = state;
                                wistiaEmbed.params.autoplay = state;

                                if (state) {
                                    $(wistiaEmbed).trigger('autoplayenabled');
                                } else {
                                    $(wistiaEmbed).trigger('autoplaydisabled');
                                }

                                $(this).spinnerIcon();
                            }
                        })
                    });

                container.append(button);
                button.spinnerIcon();
            },

            /**
             * Add focus button to container
             *
             * @param container
             */
            buttonFocus: function(container) {
                var button = $('<div>'),
                    icon   = $('<i class="fa">');

                button
                    .data({
                        icon    : icon,
                        'option': 'focus'
                    })
                    .attr('id', wistiaEmbed.uuid + '_focus_button')
                    .addClass('osc-btn focus')
                    .attr('title', 'Focus')
                    .text('Focus')
                    .prepend(icon)
                    .on('click', function(event) {
                        button.spinnerIcon(true);
                        $.Oscampus.ajax({
                            data   : {
                                task: 'wistia.toggleFocusState'
                            },
                            context: this,
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

                                $(this).spinnerIcon();
                            }
                        });
                    });

                button.spinnerIcon();
                container.append(button);

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

                // Autoplay move to the next lesson and turn focus off
                wistiaEmbed.bind('end', function(event) {
                    if (wistiaEmbed.options.autoPlay) {
                        $('#nextbut')[0].click();
                    }

                    wistiaEmbed.plugin['dimthelights'].undim();
                });
            },

            buttonDownload: function(container) {
                var button = $('<div>');
                button
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
                                    }
                                }
                            });

                        } else {
                            wistiaEmbed.pause();
                            alert('testing');
                        }
                    });
                if (!options.download.authorised) {
                    buttonDownload.addClass('osc-off');
                }
                container.append(buttonDownload);

            },

            noauthOverlay: function(container) {
                var overlay = $('<div>')
                    .attr('id', wistiaEmbed.uuid + '_overlay')
                    .addClass('wistia_overlay');
                container.parent().append(overlay);

                var wrapper = $('<div>').addClass('wrapper');
                $(overlay).append(wrapper);

                wrapper.html(
                    '<div style="padding-left: 25%; padding-right: 25%;">' + data.error + '</div>' +
                    '</a><a href="#" id="' + wistiaEmbed.uuid + '_resume_skip" class="skip">' +
                    '  <span id="' + wistiaEmbed.uuid + '_resume_skip_arrow">&nbsp;</span>' +
                    '  Skip to where you left off' +
                    '</a>'
                );

                $('#' + wistiaEmbed.uuid + '_resume_skip').click(function() {
                    overlay.fadeOut(200, function() {
                        overlay.remove();
                        wistiaEmbed.play();
                    });
                });

                function resize() {
                    overlay.css('height', $(wistiaEmbed.grid.main).height());
                    overlay.css('width', $(wistiaEmbed.grid.main).width());
                    wrapper.css('top', Math.max(0, (overlay.height() - wrapper.height()) / 2) + 'px');
                }

                resize();

                wistiaEmbed.bind('widthchange', resize);
                wistiaEmbed.bind('heightchange', resize);

                overlay.addClass('visible');
            },

            signupOverlay: function() {
                var overlay = $('<div>')
                    .attr('id', wistiaEmbed.uuid + '_overlay')
                    .addClass('wistia_overlay');
                container.append(overlay);

                var wrapper = $('<div>').addClass('wrapper');
                $(overlay).append(wrapper);

                wrapper.html(
                    '<div>Become a Pro Member<br/>to download this video!</div>' +
                    '<a href="#" id="' + wistiaEmbed.uuid + '_subscribe" class="subscribe">' +
                    '  <span id="' + wistiaEmbed.uuid + '_subscribe_icon">&nbsp;</span>' +
                    '  Subscribe as Pro to download' +
                    '</a><a href="#" id="' + wistiaEmbed.uuid + '_resume_skip" class="skip">' +
                    '  <span id="' + wistiaEmbed.uuid + '_resume_skip_arrow">&nbsp;</span>' +
                    '  Skip to where you left off' +
                    '</a>'
                );

                $('#' + wistiaEmbed.uuid + '_subscribe').click(function() {
                    window.location = downloadURL;
                });

                $('#' + wistiaEmbed.uuid + '_resume_skip').click(function() {
                    overlay.fadeOut(200, function() {
                        overlay.remove();
                        wistiaEmbed.play();
                    });
                });

                function resize() {
                    overlay.css('height', $(wistiaEmbed.grid.main).height());
                    overlay.css('width', $(wistiaEmbed.grid.main).width());
                    wrapper.css('top', Math.max(0, (overlay.height() - wrapper.height()) / 2) + 'px');
                }

                resize();

                wistiaEmbed.bind('widthchange', resize);
                wistiaEmbed.bind('heightchange', resize);

                overlay.addClass('visible');
            },

            getOverlay: function(id, text) {
                var overlay = $('<div>')
                    .attr('id', 'overlay_' + id)
                    .addClass('osc-wistia-overlay');

                var wrapper = $('<div>').addClass('wrapper');
                $(overlay).append(wrapper);

                wrapper.html(text);

                console.log($('#overlay_' + id + ' .subcribe_'))

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

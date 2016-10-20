(function($) {
    $.extend($.fn, {
        /**
         * Toggle a spinner icon with a base icon.
         * Uses the data store to determine behaviors and requires wistiaEmbed initialised
         * {
         *    {jQuery} icon     : the html element displaying the icon
         *    {string} option   : the wistiaEmbed option to determined enabled/disabled
         *    {string} disabled : The icon to use when option is disabled
         *    {string} enabled  : The icon to use when option is enabled
         * }
         *
         * @param {bool} waiting
         *
         * @returns {jQuery}
         */
        spinnerIcon: function(waiting) {
            var data          = $(this).data(),
                wistiaOptions = wistiaEmbed.options || null;

            if (data.icon && wistiaOptions) {
                var disabled = data.disabled || 'fa-square-o',
                    enabled  = data.enabled || 'fa-check-square',
                    option   = data.option || 'generic';

                if (waiting) {
                    data.icon
                        .removeClass(disabled)
                        .removeClass(enabled)
                        .addClass('fa-spinner fa-spin');
                }
                else {
                    data.icon.removeClass('fa-spinner fa-spin');

                    if (!wistiaOptions.hasOwnProperty(option) || wistiaOptions[option]) {
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

            return this;
        }
    });

    $.Oscampus = $.extend({}, $.Oscampus);

    $.Oscampus.wistia = {
        options: {
            videoId    : null,
            shortId    : null,
            mobile     : false,
            formToken  : null,
            intervalPct: 10,
            gracePct   : 3,
            volume     : 1,
            upgradeUrl : null,
            authorised : {
                download: false,
                controls: false
            },
            offClass   : 'osc-off'
        },

        /**
         * Initialise all custom video functions
         *
         * @param options
         */
        init: function(options) {
            options = $.extend(true, {}, this.options, options);

            window._wq = window._wq || [];
            this.moveNavigationButtons(options);
            this.saveVolumeChange(options);

            if (!options.mobile) {
                //    this.addExtraControls(options);
            }

            //this.setFullScreen();

            //this.setMonitoring(options);
        },

        /**
         * Setup progress recording for this video
         *
         * @param {object} options
         */
        setMonitoring: function(options) {
            var nextRecord = options.intervalPct,
                lesson     = $.Oscampus.lesson.current;

            var ajaxOptions = {
                url   : 'index.php',
                method: 'post',
                data  : {
                    option  : 'com_oscampus',
                    format  : 'json',
                    task    : 'activity.record',
                    lessonId: lesson.id,
                    score   : 0
                }
            };
            ajaxOptions.data[options.formToken] = 1;

            wistiaEmbed.bind('secondchange', function(currentSecond) {
                var currentPercent = parseInt(this.percentWatched() * 100, 10);

                // Use a grace period to consider a video completely watched
                if (currentPercent + options.gracePct > 100) {
                    currentPercent = 100;
                }

                if (currentPercent >= nextRecord) {
                    $.ajax($.extend(true, {}, ajaxOptions, {
                        data: {
                            score: currentPercent
                        }
                    }));
                    nextRecord += options.intervalPct;
                }
            });

            wistiaEmbed.bind('end', function() {
                var finalPercent = parseInt(this.percentWatched() * 100, 10);

                // Because percentWatched hardly ever returns 100% even when it's true
                if (finalPercent + options.gracePct >= 100) {
                    finalPercent = 100;
                }

                $.ajax($.extend(true, {}, ajaxOptions, {
                    data: {
                        score: finalPercent
                    }
                }));
            });
        },

        /**
         * Fullscreen switching event handling
         */
        setFullScreen: function() {
            var oldButton = $('[id^=wistia_fullscreenButton_]'),
                newButton = oldButton.clone();

            newButton.addClass('custom').addClass('w-is-visible');

            oldButton
                .after(newButton)
                .remove();

            newButton.on('click', function() {
                if (screenfull && screenfull.enabled) {
                    if (screenfull.isFullscreen) {
                        screenfull.exit();
                    } else {
                        screenfull.request($('#oscampus')[0]);
                    }
                }
            });

            // Replace the native fullscreen mode
            wistiaEmbed.fullscreenButton = newButton[0];
            wistiaEmbed.requestFullscreen = function() {
                newButton.trigger('click');
            };
            this.setFullscreenNavigation();

            if (screenfull && screenfull.enabled) {
                document.addEventListener(screenfull.raw.fullscreenchange, function() {
                    if (!screenfull.isFullscreen) {
                        // Restore the video size
                        var hashedId = wistiaEmbed.hashedId();
                        var elems = $('#wistia_' + hashedId + '_grid_wrapper, #wistia_' + hashedId + '_grid_main');
                        elems.css('width', '100%');
                        elems.css('height', '100%');
                        $('body').removeClass('fullscreen');
                    } else {
                        $('body').addClass('fullscreen');
                    }
                });

                document.addEventListener(screenfull.raw.fullscreenerror, function(event) {
                    console.error('Failed to enable fullscreen', event);
                });
            }
        },

        /**
         * Set prev/next buttons for handling full screen navigation.
         * If we're in fullscreen mode we have to do an ajax load of the next
         * wistia lesson to retain fullscreen.
         */
        setFullscreenNavigation: function() {
            var options = $.Oscampus.lesson.navigation.options;

            $.each(options.buttons, function(direction, id) {
                if ($.Oscampus.lesson[direction]) {
                    $(id).bindBefore('click', function(evt) {
                        wistiaEmbed.pause();
                        wistiaEmbed.plugin['dimthelights'].undim();

                        var target = $.Oscampus.lesson[direction];

                        if (screenfull.enabled && screenfull.isFullscreen && target.type) {
                            if (target.type === 'wistia') {
                                evt.preventDefault();

                                var container = $(options.container);

                                container.load(target.link, {tmpl: 'oscampus'}, function(text, status) {
                                    $('body').addClass('fullscreen');
                                    var postProcess = setInterval(function() {
                                        if (typeof wistiaEmbed.elem() !== 'undefined') {
                                            wistiaEmbed.ready(function() {
                                                $.Oscampus.wistia.moveNavigationButtons();
                                                $.Oscampus.wistia.setFullscreenNavigation();

                                                // Update the url and title
                                                window.history.pushState(null, target.title, target.link);
                                                document.title = target.title;

                                                // Set the custom video speed
                                                if (typeof Wistia.plugin['customspeed'] != 'undefined') {
                                                    Wistia.plugin['customspeed'](wistiaEmbed);
                                                }
                                            });

                                            clearInterval(postProcess);
                                            postProcess = null;
                                            container.removeClass('loading');
                                        }
                                    }, 500);
                                });
                            }
                        }
                    });
                }
            });
        },

        /**
         * @param {object} options
         *
         * Save volume changes in session as user navigates through videos
         */
        saveVolumeChange: function(options) {
            window._wq.push({
                id     : options.shortId,
                onReady: function(video) {
                    var saveVolume;

                    video.volume(parseFloat(options.volume));

                    video.bind('volumechange', function(level) {
                        if (saveVolume) {
                            clearTimeout(saveVolume);
                        }
                        saveVolume = setTimeout(function() {
                            $.Oscampus.ajax({
                                data   : {
                                    task : 'wistia.setVolumeLevel',
                                    level: video.volume()
                                },
                                success: function() {
                                    // response ignored
                                }
                            });
                        }, 750);
                    });
                }
            });
        },

        /**
         * Move standard navigation buttons into the video area itself
         */
        moveNavigationButtons: function(options) {
            var container = $('#course-navigation'),
                buttons   = $('.osc-btn, #course-navigation');

            container.css('z-index', '999999');

            var hideWistiaButtons = function() {
                buttons.removeClass('osc-visible');
                buttons.addClass('osc-hidden');
            };

            var showWistiaButtons = function() {
                buttons.addClass('osc-visible');
                buttons.removeClass('osc-hidden');
            };

            $('.osc-btn').hover(showWistiaButtons);

            window._wq = window._wq || [];
            window._wq.push({
                id     : options.shortId,
                onReady: function(video) {
                    $(video.grid.top_inside).append(container);

                    // Events
                    $(video.grid.main)
                        .mouseenter(showWistiaButtons)
                        .mousemove(showWistiaButtons)
                        .mouseleave(hideWistiaButtons);

                    video.bind('play', hideWistiaButtons);
                    video.bind('pause', hideWistiaButtons);
                }
            });
        },

        /**
         * Create additional control buttons
         *
         * @param options
         */
        addExtraControls: function(options) {
            // Add the container for the buttons
            var container = $('<div>')
                .attr('id', wistiaEmbed.uuid + '_buttons_container')
                .addClass('osc-lesson-options osc-btn-group');
            $(wistiaEmbed.grid.top_inside).append(container);

            this.buttonDownload(container, options);

            if (options.authorised.controls) {
                this.buttonAutoplay(container, options);
                this.buttonFocus(container, options);
            }
        },

        /**
         * Add autoplay button to the container
         *
         * @param {object} container
         * @param {object} options
         */
        buttonAutoplay: function(container, options) {
            var button = this.createButton('autoplay', Joomla.JText._('COM_OSCAMPUS_VIDEO_AUTOPLAY'))

            if (wistiaEmbed.options.autoPlay) {
                button.removeClass(options.offClass);
            } else {
                button.addClass(options.offClass);
            }

            button
                .data('option', 'autoPlay')
                .spinnerIcon()
                .on('click', function(event) {
                    $(this).spinnerIcon(true);
                    $.Oscampus.ajax({
                        context : this,
                        data    : {
                            task: 'wistia.toggleAutoPlayState'
                        },
                        success : function(state) {
                            wistiaEmbed.options.autoPlay = state;
                            wistiaEmbed.params.autoPlay = state;

                            if (state) {
                                $(wistiaEmbed).trigger('autoplayenabled');
                                button.removeClass(options.offClass);
                            } else {
                                $(wistiaEmbed).trigger('autoplaydisabled');
                                button.addClass(options.offClass);
                            }
                        },
                        complete: function() {
                            $(this).spinnerIcon();
                        }
                    })
                });

            container.append(button);
        },

        /**
         * Add focus button to container
         *
         * @param {object} container
         * @param {object} options
         */
        buttonFocus: function(container, options) {
            var button = this.createButton('focus', Joomla.JText._('COM_OSCAMPUS_VIDEO_FOCUS'));

            if (wistiaEmbed.options.focus) {
                button.removeClass(options.offClass)
            } else {
                button.addClass(options.offClass);
            }

            button
                .data('option', 'focus')
                .spinnerIcon()
                .on('click', function(event) {
                    $(this).spinnerIcon(true);
                    $.Oscampus.ajax({
                        data    : {
                            task: 'wistia.toggleFocusState'
                        },
                        context : this,
                        success : function(state) {
                            wistiaEmbed.options.focus = state;
                            wistiaEmbed.params.focus = state;

                            if (state) {
                                wistiaEmbed.plugin['dimthelights'].dim();
                                $(wistiaEmbed).trigger('focusenabled');
                                button.removeClass(options.offClass);
                            } else {
                                wistiaEmbed.plugin['dimthelights'].undim();
                                $(wistiaEmbed).trigger('focusdisabled');
                                button.addClass(options.offClass);
                            }
                        },
                        complete: function() {
                            $(this).spinnerIcon();
                        }
                    });
                });

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

        /**
         * Add download button
         *
         * @param container
         * @param options
         */
        buttonDownload: function(container, options) {
            options = $.extend({}, this.options, options);

            var button = this.createButton('download', Joomla.JText._('COM_OSCAMPUS_VIDEO_DOWNLOAD'));
            if (!options.authorised.download) {
                button.addClass(options.offClass);
            }

            button
                .data('enabled', 'fa-cloud-download')
                .spinnerIcon()
                .on('click', function(event) {
                    if (options.authorised.download) {
                        $(this).spinnerIcon(true);
                        wistiaEmbed.pause();

                        // Get the download limit information
                        $.Oscampus.ajax({
                            data    : {
                                task: 'wistia.downloadLimit'
                            },
                            context : this,
                            success : function(response) {
                                if (response.authorised) {
                                    var formId = 'formDownload-' + wistiaEmbed.hashedId();
                                    var form = $("#" + formId);

                                    if (form.length == 0) {
                                        form = $('<form>');

                                        var query = {
                                            option: 'com_oscampus',
                                            task  : 'wistia.download',
                                            id    : wistiaEmbed.hashedId(),
                                            format: 'raw'
                                        };

                                        form.attr({
                                            id    : formId,
                                            method: 'POST',
                                            action: 'index.php?' + $.param(query)
                                        });

                                        if (options.formToken) {
                                            var tokenField = $('<input>');
                                            tokenField.attr({
                                                type : 'hidden',
                                                name : options.formToken,
                                                value: 1
                                            });
                                            form.append(tokenField);
                                        }

                                        $('body').append(form);
                                    }
                                    form.submit();

                                } else {
                                    $.Oscampus.wistia.overlay.refused(response.error);
                                }
                            },
                            complete: function() {
                                $(this).spinnerIcon();
                            }
                        });

                    } else {
                        wistiaEmbed.pause();

                        $.Oscampus.wistia.overlay.upgrade(options.upgradeUrl);
                    }
                });

            container.append(button);
        },

        /**
         * Overlay factory for creating messages over the wistia video
         */
        overlay: {
            /**
             * The base overlay container
             *
             * @param {string} html
             *
             * @returns {jQuery}
             */
            base: function(html) {
                var overlay = $('<div>')
                    .attr('id', wistiaEmbed.uuid + '_overlay')
                    .addClass('wistia_overlay')
                    .css({
                        height: $(wistiaEmbed.grid.main).height(),
                        width : $(wistiaEmbed.grid.main).width()
                    });

                $(wistiaEmbed.grid.main).append(overlay);

                var wrapper = $('<div>').addClass('wrapper');
                $(overlay).append(wrapper);

                wrapper.html(html);

                this.resize(overlay, wrapper);

                wistiaEmbed.bind('widthchange', this.resize(overlay, wrapper));
                wistiaEmbed.bind('heightchange', this.resize(overlay, wrapper));

                overlay.addClass('visible');

                $('#' + wistiaEmbed.uuid + '_resume_skip').click(function() {
                    overlay.fadeOut(200, function() {
                        overlay.remove();
                        wistiaEmbed.play();
                    });
                });

                return overlay;
            },

            /**
             * Resize the overlay container based on current conditions
             *
             * @param {jQuery} overlay
             * @param {jQuery} wrapper
             */
            resize: function(overlay, wrapper) {
                wrapper.css('top', Math.max(0, (overlay.height() - wrapper.height()) / 2) + 'px');
            },

            /**
             * Create the not-authorised/suggest upgrade overlay
             */
            upgrade: function(upgradeUrl) {
                var overlay = this.base(
                    '<div>' + Joomla.JText._('COM_OSCAMPUS_VIDEO_DOWNLOAD_UPGRADE') + '</div>'
                    + '<a href="' + upgradeUrl + '" id="' + wistiaEmbed.uuid + '_subscribe" class="subscribe">'
                    + '<span id="' + wistiaEmbed.uuid + '_subscribe_icon">&nbsp;</span>'
                    + Joomla.JText._('COM_OSCAMPUS_VIDEO_DOWNLOAD_UPGRADE_LINK')
                    + '</a><a href="#" id="' + wistiaEmbed.uuid + '_resume_skip" class="skip">'
                    + '  <span id="' + wistiaEmbed.uuid + '_resume_skip_arrow">&nbsp;</span>'
                    + Joomla.JText._('COM_OSCAMPUS_VIDEO_RESUME')
                    + '</a>'
                );

                $('#' + wistiaEmbed.uuid + '_subscribe').click(function() {
                    window.location = downloadURL;
                });
            },

            /**
             * Download request was refused overlay
             *
             * @param message
             */
            refused: function(message) {
                var overlay = this.base(
                    '<div style="padding-left: 25%; padding-right: 25%;">' + message + '</div>'
                    + '</a><a href="#" id="' + wistiaEmbed.uuid + '_resume_skip" class="skip">'
                    + '  <span id="' + wistiaEmbed.uuid + '_resume_skip_arrow">&nbsp;</span>'
                    + Joomla.JText._('COM_OSCAMPUS_VIDEO_RESUME')
                    + '</a>'
                );
            }
        },

        /**
         * Create the base for standard custom control buttons
         *
         * @param {string} name
         * @param {string} title
         *
         * @returns {jQuery}
         */
        createButton: function(name, title) {
            var button = $('<div>'),
                icon   = $('<i class="fa">'),
                id     = wistiaEmbed.uuid + '_' + name + '_button';

            button
                .data('icon', icon)
                .attr('id', id)
                .addClass('osc-btn ' + name)
                .attr('title', title)
                .text(title)
                .prepend(icon);

            return button;
        }
    };
})(jQuery);

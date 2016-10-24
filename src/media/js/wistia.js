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
         * @param {object} wistiaOptions
         * @param {bool} waiting
         *
         * @returns {jQuery}
         */
        spinnerIcon: function(wistiaOptions, waiting) {
            var data = $(this).data();

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
         * Initialise all custom video functions. Call with:
         *
         * window._wq = window._wq || [];
         * window._wq.push({
         *    id: {hashedId},
         *    onReady: function(video) {
         *       $.Oscampus.wistia.init(video, {$options});
         *    }
         * });
         *
         * @param {object} video
         * @param {object} options
         */
        init: function(video, options) {
            options = $.extend(true, {}, this.options, options);

            this.saveVolumeChange(video, options);

            this.customControls.add(video, options);
            this.moveNavigationButtons(video, options);
            this.setFullScreen(video, options);

            //this.setMonitoring(options);

            if (this.postfix) {
                var lesson = $.Oscampus.lesson;

                // Update the url and title
                window.history.pushState(null, lesson.current.title, lesson.current.link);
                document.title = lesson.current.title;

                // Set the custom video speed
                if (Wistia.plugin['customspeed']) {
                    Wistia.plugin['customspeed'](video);
                }

                $(lesson.navigation.options.container).removeClass('loading');
            }
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
         *
         * @param {object} video
         * @param {object} options
         */
        setFullScreen: function(video, options) {
            var oldButton = $('#wistia_' + video.hashedId() + ' [id^=wistia_fullscreenButton_]'),
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

            $.Oscampus.wistia.setFullscreenNavigation(video, options);

            if (screenfull && screenfull.enabled) {
                var test = function() {
                    if (!screenfull.isFullscreen) {
                        // Restore the video size
                        var hashedId = video.hashedId();
                        var elems = $('#wistia_' + hashedId + '_grid_wrapper, #wistia_' + hashedId + '_grid_main');
                        elems.css('width', '100%');
                        elems.css('height', '100%');
                        $('body').removeClass('fullscreen');

                    } else {
                        $('body').addClass('fullscreen');
                    }
                };
                document.removeEventListener(screenfull.raw.fullscreenchange, test);
                document.addEventListener(screenfull.raw.fullscreenchange, test);

                var fullscreenError = function(event) {
                    console.error('Failed to enable fullscreen', event);
                };
                document.removeEventListener(screenfull.raw.fullscreenerror, fullscreenError);
                document.addEventListener(screenfull.raw.fullscreenerror, fullscreenError);
            }
        },

        /**
         * Set prev/next buttons for handling full screen navigation.
         * If we're in fullscreen mode we have to do an ajax load of the next
         * wistia lesson to retain fullscreen.
         *
         * @param {object} video
         * @param {object} options
         */
        setFullscreenNavigation: function(video, options) {
            var lesson = $.Oscampus.lesson;

            if (lesson.navigation.options.buttons) {
                var buttons = lesson.navigation.options.buttons;
                $.each(buttons, function(direction, id) {
                    if (lesson[direction]) {
                        $(id).bindBefore('click', function(evt) {
                            video.pause();
                            if (video.plugin['dimthelights']) {
                                video.plugin['dimthelights'].undim();
                            }

                            var target = lesson[direction];

                            if (screenfull
                                && screenfull.enabled
                                && screenfull.isFullscreen
                                && target.type
                                && target.type === 'wistia'
                            ) {
                                evt.preventDefault();

                                var container = lesson.navigation.options.container;
                                $.get(target.link, {format: 'raw'}, function(data) {
                                    $('body').addClass('fullscreen');
                                    $(container).html(data);

                                    $.Oscampus.wistia.postfix = true;
                                });
                            }
                        });
                    }
                });
            }
        },

        /**
         * @param {object} video
         * @param {object} options
         *
         * Save volume changes in session as user navigates through videos
         */
        saveVolumeChange: function(video, options) {
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
        },

        /**
         * @param {object} video
         * @param {object} options
         *
         * Move standard navigation buttons into the video area itself
         */
        moveNavigationButtons: function(video, options) {
            var container = $('#course-navigation'),
                buttons   = $('.osc-btn, #course-navigation');

            container.css('z-index', '99999');

            var hideWistiaButtons = function() {
                buttons.removeClass('osc-visible');
                buttons.addClass('osc-hidden');
            };

            var showWistiaButtons = function() {
                buttons.addClass('osc-visible');
                buttons.removeClass('osc-hidden');
            };

            $('.osc-btn').hover(showWistiaButtons);

            $(video.grid.top_inside).append(container);

            // Events
            $(video.grid.main)
                .mouseenter(showWistiaButtons)
                .mousemove(showWistiaButtons)
                .mouseleave(hideWistiaButtons);

            video.bind('play', hideWistiaButtons);
            video.bind('pause', hideWistiaButtons);
        },

        customControls: {
            video  : null,
            options: null,

            add: function(video, options) {
                if (!options.mobile) {
                    this.video = video;
                    this.options = options;

                    // Create a new container
                    var container = $('<div>')
                        .attr('id', video.uuid + '_buttons_container')
                        .addClass('osc-lesson-options osc-btn-group')
                        .css('z-index', '99999');

                    $(video.grid.top_inside).append(container);

                    container.append(this.getDownload());
                    if (options.authorised.controls) {
                        container.append(this.getAutoplay());
                        container.append(this.getFocus());
                    }
                }
            },

            /**
             * get s download button
             *
             * @return {jQuery}
             */
            getDownload: function() {
                var video    = this.video,
                    options  = this.options,
                    hashedId = this.video.hashedId(),
                    button   = this.createButton('download', Joomla.JText._('COM_OSCAMPUS_VIDEO_DOWNLOAD'));

                if (!options.authorised.download) {
                    button.addClass(this.options.offClass);
                }

                button
                    .data('enabled', 'fa-cloud-download')
                    .spinnerIcon(video.options)
                    .on('click', function(event) {
                        if (options.authorised.download) {
                            $(this).spinnerIcon(video.options, true);
                            video.pause();

                            // Get the download limit information
                            $.Oscampus.ajax({
                                data    : {
                                    task: 'wistia.downloadLimit'
                                },
                                context : this,
                                success : function(response) {
                                    if (response.authorised) {
                                        var formId = 'formDownload-' + hashedId;
                                        var form = $("#" + formId);

                                        if (form.length == 0) {
                                            form = $('<form>');

                                            var query = {
                                                option: 'com_oscampus',
                                                task  : 'wistia.download',
                                                id    : hashedId,
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
                                        $.Oscampus.wistia.overlay.refused(video, response.error);
                                    }
                                },
                                complete: function() {
                                    $(this).spinnerIcon(video.options);
                                }
                            });

                        } else {
                            video.pause();

                            $.Oscampus.wistia.overlay.upgrade(video, options.upgradeUrl);
                        }
                    });

                return button;
            },

            /**
             * Add autoplay button to the container
             *
             * @return {jQuery}
             */
            getAutoplay: function() {
                var video   = this.video,
                    options = this.options,
                    button  = this.createButton('autoplay', Joomla.JText._('COM_OSCAMPUS_VIDEO_AUTOPLAY'));

                if (video.options.autoPlay) {
                    button.removeClass(options.offClass);
                } else {
                    button.addClass(options.offClass);
                }

                button
                    .data('option', 'autoPlay')
                    .spinnerIcon(video.options)
                    .on('click', function(event) {
                        $(this).spinnerIcon(video.options, true);
                        $.Oscampus.ajax({
                            context : this,
                            data    : {
                                task: 'wistia.toggleAutoPlayState'
                            },
                            success : function(state) {
                                video.options.autoPlay = state;
                                video.params.autoPlay = state;

                                if (state) {
                                    $(video).trigger('autoplayenabled');
                                    button.removeClass(options.offClass);
                                } else {
                                    $(video).trigger('autoplaydisabled');
                                    button.addClass(options.offClass);
                                }
                            },
                            complete: function() {
                                $(this).spinnerIcon(video.options);
                            }
                        })
                    });

                // Autoplay move to the next lesson and turn focus off
                video.bind('end', function(event) {
                    var nextButton = $($.Oscampus.lesson.navigation.options.buttons.next);
                    if (video.options.autoPlay && nextButton) {
                        nextButton.click();
                    }

                    if (video.plugin['dimthelights']) {
                        video.plugin['dimthelights'].undim();
                    }
                });

                return button;
            },

            /**
             * Add focus button to container
             *
             * @return {jQuery}
             */
            getFocus: function() {
                var video   = this.video,
                    options = this.options,
                    button  = this.createButton('focus', Joomla.JText._('COM_OSCAMPUS_VIDEO_FOCUS'));

                if (video.options.focus) {
                    button.removeClass(options.offClass)
                } else {
                    button.addClass(options.offClass);
                }

                button
                    .data('option', 'focus')
                    .spinnerIcon(video.options)
                    .on('click', function(event) {
                        $(this).spinnerIcon(video.options, true);
                        $.Oscampus.ajax({
                            data    : {
                                task: 'wistia.toggleFocusState'
                            },
                            context : this,
                            success : function(state) {
                                video.options.focus = state;
                                video.params.focus = state;

                                if (state) {
                                    video.plugin['dimthelights'].dim();
                                    $(video).trigger('focusenabled');
                                    button.removeClass(options.offClass);
                                } else {
                                    video.plugin['dimthelights'].undim();
                                    $(video).trigger('focusdisabled');
                                    button.addClass(options.offClass);
                                }
                            },
                            complete: function() {
                                $(this).spinnerIcon(video.options);
                            }
                        });
                    });

                // Fix the focus on play
                video.bind('play', function(event) {
                    if (video.options.focus) {
                        // Uses interval to make sure the plugin is loaded before call it
                        var interval = setInterval(function() {
                            if (video.plugin['dimthelights']) {
                                video.plugin['dimthelights'].dim();
                                clearInterval(interval);
                                interval = null;
                            }
                        }, 500);
                    }
                });

                return button;
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
                    id     = this.video.uuid + '_' + name + '_button';

                button
                    .data('icon', icon)
                    .attr('id', id)
                    .addClass('osc-btn ' + name)
                    .attr('title', title)
                    .text(title)
                    .prepend(icon);

                return button;
            }

        },

        /**
         * Overlay factory for creating messages over the wistia video
         */
        overlay: {
            /**
             * The base overlay container
             *
             * @param {object} video
             * @param {string} html
             *
             * @returns {jQuery}
             */
            base: function(video, html) {
                var overlay = $('<div>')
                    .attr('id', video.uuid + '_overlay')
                    .addClass('wistia_overlay')
                    .css({
                        height: $(video.grid.main).height(),
                        width : $(video.grid.main).width()
                    });

                $(video.grid.main).append(overlay);

                var wrapper = $('<div>').addClass('wrapper');
                $(overlay).append(wrapper);

                wrapper.html(html);

                this.resize(overlay, wrapper);

                video.bind('widthchange', this.resize(overlay, wrapper));
                video.bind('heightchange', this.resize(overlay, wrapper));

                overlay.addClass('visible');

                $('#' + wistiaEmbed.uuid + '_resume_skip').click(function() {
                    overlay.fadeOut(200, function() {
                        overlay.remove();
                        video.play();
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
             *
             * @param {object} video
             * @param {string} upgradeUrl
             */
            upgrade: function(video, upgradeUrl) {
                var overlay = this.base(
                    video,
                    '<div>' + Joomla.JText._('COM_OSCAMPUS_VIDEO_DOWNLOAD_UPGRADE') + '</div>'
                    + '<a href="' + upgradeUrl + '" id="' + video.uuid + '_subscribe" class="subscribe">'
                    + '<span id="' + video.uuid + '_subscribe_icon">&nbsp;</span>'
                    + Joomla.JText._('COM_OSCAMPUS_VIDEO_DOWNLOAD_UPGRADE_LINK')
                    + '</a><a href="#" id="' + video.uuid + '_resume_skip" class="skip">'
                    + '  <span id="' + video.uuid + '_resume_skip_arrow">&nbsp;</span>'
                    + Joomla.JText._('COM_OSCAMPUS_VIDEO_RESUME')
                    + '</a>'
                );

                $('#' + video.uuid + '_subscribe').click(function() {
                    window.location = downloadURL;
                });
            },

            /**
             * Download request was refused overlay
             *
             * @param {object} video
             * @param {string} message
             */
            refused: function(video, message) {
                var overlay = this.base(
                    video,
                    '<div style="padding-left: 25%; padding-right: 25%;">' + message + '</div>'
                    + '</a><a href="#" id="' + video.uuid + '_resume_skip" class="skip">'
                    + '  <span id="' + video.uuid + '_resume_skip_arrow">&nbsp;</span>'
                    + Joomla.JText._('COM_OSCAMPUS_VIDEO_RESUME')
                    + '</a>'
                );
            }
        }
    };
})(jQuery);

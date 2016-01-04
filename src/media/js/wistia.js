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

    $.Oscampus = $.extend({}, $.Oscampus, {
        wistia: {
            options: {
                extras   : false,
                formToken: null,
                download : {
                    authorised: false
                }
            },

            /**
             * Initialise all custom video functions
             *
             * @param options
             */
            init: function(options) {
                options = $.extend(true, {}, this.options, options);

                this.moveNavigationButtons();
                this.saveVolumeChange();

                if (options.extras) {
                    this.addExtraControls(options);
                }

                this.fullScreen.init();
            },

            /**
             * Fullscreen switching event handling
             */
            fullScreen: {
                init: function() {
                    this.replaceNative();
                    //this.setNavigation();
                },

                /**
                 * Replaces the native fullscreen button
                 */
                replaceNative: function() {
                    var oldButton = $('[id^=wistia_fullscreenButton_]'),
                        newButton = oldButton.clone();

                    newButton
                        .addClass('custom')
                        .css({
                            width : '50px',
                            height: '32px',
                            right : 0
                        });

                    oldButton
                        .after(newButton)
                        .remove();

                    wistiaEmbed.grid.center.addEventListener('mouseenter', function(evt) {
                        newButton.removeClass('wistia_romulus_hidden');
                        newButton.addClass('wistia_romulus_visible');
                    });

                    wistiaEmbed.grid.center.addEventListener('mouseleave', function(evt) {
                        newButton.removeClass('wistia_romulus_visible');
                        newButton.addClass('wistia_romulus_hidden');
                    });

                    newButton.on('click', function() {
                        if (screenfull.enabled) {
                            if (screenfull.isFullscreen) {
                                screenfull.exit();
                            } else {
                                screenfull.request($('main')[0]);
                            }
                        }
                    });

                    // Replace the native fullscreen mode
                    wistiaEmbed.fullscreenButton = newButton[0];
                    wistiaEmbed.requestFullscreen = function() {
                        newButton.trigger('click');
                    };

                    if (screenfull.enabled) {
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

                setNavigation: function() {
                    var next_type = $('#next_type').val(),
                        prev_type = $('#prev_type').val();

                    if (next_type !== 'quiz' && next_type !== 'false') {
                        $('#nextbut').on('click', onClickButton);
                    }

                    if (prev_type !== 'quiz' && prev_type !== 'false') {
                        $('#prevbut').on('click', onClickButton);
                    }

                    wistiaEmbed.bind('play', function(event) {
                        $('#guru_content').removeClass('loading');
                    });
                },

                onClick: function(event) {
                    event.preventDefault();
                    var $delegateTarget = $(event.delegateTarget),
                        $guruContent    = $('#guru_content'),
                        url             = $delegateTarget.attr('href'),
                        isNext          = $delegateTarget.attr('id') == 'nextbut',
                        loadingMsg      = '';

                    url += (url.search(/\?/) >= 0) ? '&' : '?';
                    url += 'tmpl=component';

                    wistiaEmbed.pause();
                    wistiaEmbed.plugin['dimthelights'].undim();

                    // Show a "loading" message
                    if ($delegateTarget.attr('id') === 'nextbut') {
                        loadingMsg = 'Loading next lesson: ' + $('#next_name').val();
                    } else {
                        loadingMsg = 'Loading previous lesson: ' + $('#prev_name').val();
                    }
                    $guruContent.html('<span class="message">' + loadingMsg + '</span>');
                    $guruContent.addClass('loading');

                    document.title = 'Loading...';

                    $guruContent.load(url, {}, function() {
                        var interval = setInterval(function() {
                            if (typeof wistiaEmbed.elem() !== 'undefined') {
                                wistiaEmbed.ready(function() {
                                    $('#guru_content').removeClass('loading');

                                    fixFullscreen();
                                    setNextPreviousButtonEvents();

                                    // Update the url and title
                                    var title = $('#step_title').text();
                                    window.history.pushState(null, title, url.replace(/[&\?]tmpl=component/, ''));
                                    document.title = title;

                                    // Set the custom video speed
                                    if (typeof Wistia.plugin['customspeed'] != 'undefined') {
                                        Wistia.plugin['customspeed'](wistiaEmbed);
                                    }
                                });

                                clearInterval(interval);
                                interval = null;
                            }
                        }, 500);
                    });
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

            /**
             * Move standard navigation buttons into the video area itself
             */
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
                this.buttonAutoplay(container);
                this.buttonFocus(container);
            },

            /**
             * Add autoplay button to the container
             *
             * @param container
             */
            buttonAutoplay: function(container) {
                var button = this.createButton('autoplay', 'Autoplay')

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
                                wistiaEmbed.params.autoplay = state;

                                if (state) {
                                    $(wistiaEmbed).trigger('autoplayenabled');
                                } else {
                                    $(wistiaEmbed).trigger('autoplaydisabled');
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
             * @param container
             */
            buttonFocus: function(container) {
                var button = this.createButton('focus', 'Focus');

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
                                } else {
                                    wistiaEmbed.plugin['dimthelights'].undim();
                                    $(wistiaEmbed).trigger('focusdisabled');
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

                var button = this.createButton('download', 'Download');
                if (!options.download.authorised) {
                    button.addClass('osc-off');
                }

                button
                    .data('enabled', 'fa-cloud-download')
                    .spinnerIcon()
                    .on('click', function(event) {
                        if (options.download.authorised) {
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
                                        $.Oscampus.wistia.overlay.refused(data.error);
                                    }
                                },
                                complete: function() {
                                    $(this).spinnerIcon();
                                }
                            });

                        } else {
                            wistiaEmbed.pause();
                            $.Oscampus.wistia.overlay.signup();
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
                 * Create the not-authorised/suggest signup overlay
                 */
                signup: function() {
                    var overlay = this.base(
                        '<div>' + Joomla.JText.COM_OSCAMPUS_WISTIA_DOWNLOAD_SIGNUP + '</div>'
                        + '<a href="#" id="' + wistiaEmbed.uuid + '_subscribe" class="subscribe">'
                        + '<span id="' + wistiaEmbed.uuid + '_subscribe_icon">&nbsp;</span>'
                        + Joomla.JText.COM_OSCAMPUS_WISTIA_DOWNLOAD_SUBSCRIBE
                        + '</a><a href="#" id="' + wistiaEmbed.uuid + '_resume_skip" class="skip">'
                        + '  <span id="' + wistiaEmbed.uuid + '_resume_skip_arrow">&nbsp;</span>'
                        + Joomla.JText.COM_OSCAMPUS_WISTIA_RESUME
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
                        + Joomla.JText.COM_OSCAMPUS_WISTIA_RESUME
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
        }
    });
})(jQuery);

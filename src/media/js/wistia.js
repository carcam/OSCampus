(function($) {
    $.extend($.fn, {
        spinnerIcon: function(waiting) {
            var data = $(this).data();

            if (data.icon) {
                var disabled      = data.disabled || 'fa-square-o',
                    enabled       = data.enabled || 'fa-check-square',
                    option        = data.option || 'generic',
                    wistiaOptions = wistiaEmbed.options || null;

                if (waiting) {
                    data.icon
                        .removeClass(disabled)
                        .removeClass(enabled)
                        .addClass('fa-spinner fa-spin');
                } else {
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

            init: function(options) {
                options = $.extend(true, {}, this.options, options);

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

            overlay: {
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

                resize: function(overlay, wrapper) {
                    wrapper.css('top', Math.max(0, (overlay.height() - wrapper.height()) / 2) + 'px');
                },

                signup: function() {
                    var overlay = this.base(
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
                },

                refused: function(message) {
                    var overlay = this.base(
                        '<div style="padding-left: 25%; padding-right: 25%;">' + message + '</div>' +
                        '</a><a href="#" id="' + wistiaEmbed.uuid + '_resume_skip" class="skip">' +
                        '  <span id="' + wistiaEmbed.uuid + '_resume_skip_arrow">&nbsp;</span>' +
                        '  Skip to where you left off' +
                        '</a>'
                    );
                }
            },

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

(function($) {
    $.Oscampus = $.extend({}, $.Oscampus, {
        wistia: {
            options: {
                download: {
                    authorised: false,
                    formToken : null
                }
            },

            fixVideoSizeProportion: function() {
                var i = 0;

                var interval = setInterval(function() {
                    var width = wistiaEmbed.videoWidth();
                    var height = wistiaEmbed.videoHeight();

                    var rate = height / width * 100;
                    $('#wistia_' + wistiaEmbed.hashedId()).parent().css('padding-bottom', rate + '%');

                    if ((i++) >= 30) {
                        clearInterval(interval);
                        interval = null;
                    }
                }, 500);
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
                    .addClass('wistia_buttons_container');
                $(wistiaEmbed.grid.top_inside).append(container);

                /*********** BEGIN DOWNLOAD ***************/
                var buttonDownload = $('<div>')
                    .attr('id', wistiaEmbed.uuid + '_download_button')
                    .addClass('wistia_button download')
                    .attr('title', 'Download')
                    .text('Download')
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
                                            if (options.download.formToken) {
                                                query[1] = options.download.formToken;
                                            }

                                            form = $('<form>').attr({
                                                id    : formId,
                                                method: 'GET',
                                                action: 'index.php?' + $.param(query)
                                            });

                                            form.css('visible', 'hidden');
                                            $('body').append(form);
                                        }
                                        //form.submit();
                                        console.log(form);

                                    } else {
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

                                        var resize = function() {
                                            overlay.css('height', $(wistiaEmbed.grid.main).height());
                                            overlay.css('width', $(wistiaEmbed.grid.main).width());
                                            wrapper.css('top', Math.max(0, (overlay.height() - wrapper.height()) / 2) + 'px');
                                        };

                                        resize();
                                        wistiaEmbed.bind('widthchange', resize);
                                        wistiaEmbed.bind('heightchange', resize);

                                        overlay.addClass('visible');
                                    }
                                }
                            });

                        } else {
                            wistiaEmbed.pause();

                            // TODO: create a method to easy show custom overlays
                            var overlay = $('<div>')
                                .attr('id', wistiaEmbed.uuid + '_overlay')
                                .addClass('wistia_overlay');
                            container.append(overlay);

                            var wrapper = $('<div>').addClass('wrapper');
                            $(overlay).append(wrapper);

                            wrapper.html(
                                '<div>Become a Pro Member<br>to download this video!</div>' +
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

                            var resize = function() {
                                overlay.css('height', $(wistiaEmbed.grid.main).height());
                                overlay.css('width', $(wistiaEmbed.grid.main).width());
                                wrapper.css('top', Math.max(0, (overlay.height() - wrapper.height()) / 2) + 'px');
                            };

                            resize();
                            wistiaEmbed.bind('widthchange', resize);
                            wistiaEmbed.bind('heightchange', resize);

                            overlay.addClass('visible');
                        }
                    });

                if (!options.download.authorised) {
                    buttonDownload.addClass('off');
                }
                container.append(buttonDownload);
                /*********** END DOWNLOAD ***************/

                /*********** BEGIN AUTOPLAY ***************/
                var buttonAutoplay = $('<div>')
                    .attr('id', wistiaEmbed.uuid + '_autoplay_button')
                    .addClass('wistia_button autoplay')
                    .attr('title', 'Autoplay')
                    .text('Autoplay')
                    .on('click', function(event) {
                        $.Oscampus.ajax({
                            data   : {
                                task: 'wistia.toggleAutoPlayState'
                            },
                            success: function(state) {
                                wistiaEmbed.options.autoPlay = state;
                                wistiaEmbed.params.autoplay = state;
                                if (state) {
                                    $(event.target).removeClass('off');
                                    $(wistiaEmbed).trigger('autoplayenabled');
                                } else {
                                    $(event.target).addClass('off');
                                    $(wistiaEmbed).trigger('autoplaydisabled');
                                }
                            }
                        })
                    });
                if (wistiaEmbed.options.autoPlay === false) {
                    button.addClass('off');
                }
                container.append(buttonAutoplay);

                // Autoplay move to the next lesson
                wistiaEmbed.bind('end', function(event) {
                    if (wistiaEmbed.options.autoPlay) {
                        $('#nextbut').click();
                    }
                });
                /*********** END AUTOPLAY ***************/

                /*********** BEGIN FOCUS ***************/
               var  buttonFocus = $('<div>')
                    .attr('id', wistiaEmbed.uuid + '_focus_button')
                    .addClass('wistia_button focus')
                    .attr('title', 'Focus')
                    .text('Focus')
                    .on('click', function(event) {
                        $.Oscampus.ajax({
                            data   : {
                                task: 'wistia.toggleFocusState'
                            },
                            success: function(state) {
                                wistiaEmbed.params.focus = state;
                                if (state) {
                                    $(event.target).removeClass('off');
                                    wistiaEmbed.plugin['dimthelights'].dim();
                                    $(wistiaEmbed).trigger('focusenabled');
                                } else {
                                    $(event.target).addClass('off');
                                    wistiaEmbed.plugin['dimthelights'].undim();
                                    $(wistiaEmbed).trigger('focusdisabled');
                                }
                            }
                        });
                    });
                if (wistiaEmbed.options.focus === false) {
                    buttonFocus.addClass('off');
                }
                container.append(buttonFocus);
                /*********** END FOCUS ***************/
            },

            moveNavigationButtons: function() {
                $(wistiaEmbed.grid.top_inside).append($('#course-navigation'));

                // Events
                var gridMain = $(wistiaEmbed.grid.main);

                var hideWistiaButtons = function() {
                    var buttons = $('.wistia_button, #course-navigation');

                    buttons.removeClass('visible');
                    buttons.addClass('hidden');
                };

                var showWistiaButtons = function() {
                    var buttons = $('.wistia_button, #course-navigation');

                    buttons.addClass('visible');
                    buttons.removeClass('hidden');
                };

                gridMain.mouseenter(showWistiaButtons);
                gridMain.mousemove(showWistiaButtons);
                gridMain.mouseleave(hideWistiaButtons);
                $('.wistia_button').hover(showWistiaButtons);

                wistiaEmbed.bind('play', hideWistiaButtons);
                wistiaEmbed.bind('pause', hideWistiaButtons);
            }
        }
    });
})(jQuery);

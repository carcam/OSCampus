(function($) {
    $.Oscampus = $.extend({}, $.Oscampus, {
        wistia: {
            controlsOptions: {
                download: {
                    authorised: false,
                    url       : null,
                    formToken : null,
                    limitUrl  : null
                },
                autoplay: {
                    url: null // Was: "/index.php?option=com_guru&controller=guruTasks&task=toggleAutoPlayState&format=json"
                },
                focus   : {
                    url: null // Was: "/index.php?option=com_guru&controller=guruTasks&task=toggleFocusState&format=json"
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

            addExtraControls: function(options) {
                options = $.extend(this.controlsOptions, options);
                var resize;

                // Add the container for the buttons
                var container = $('<div>')
                    .attr('id', wistiaEmbed.uuid + '_buttons_container')
                    .addClass('wistia_buttons_container');
                $(wistiaEmbed.grid.top_inside).append(container);

                // Extra-controls - Download
                var button = $('<div>')
                    .attr('id', wistiaEmbed.uuid + '_download_button')
                    .addClass('wistia_button download')
                    .attr('title', 'Download')
                    .text('Download')
                    .on('click', function(event) {
                        if (options.download.authorised) {
                            wistiaEmbed.pause();

                            // Get the download limit information
                            $.get(options.download.limitUrl, function(data) {
                                data = JSON.parse(data);

                                downloadLimitPeriod = data.downloadLimitPeriod + ' day';
                                if (parseInt(data.downloadLimitPeriod) > 1) {
                                    downloadLimitPeriod += 's';
                                }

                                if (!data.authorised) {
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

                                    resize = function() {
                                        overlay.css('height', $(wistiaEmbed.grid.main).height());
                                        overlay.css('width', $(wistiaEmbed.grid.main).width());
                                        wrapper.css('top', Math.max(0, (overlay.height() - wrapper.height()) / 2) + 'px');
                                    }

                                    resize();
                                    wistiaEmbed.bind('widthchange', resize);
                                    wistiaEmbed.bind('heightchange', resize);

                                    overlay.addClass('visible');
                                } else {
                                    // Replace the %s with the video ID
                                    downloadURL = options.download.url.replace('%s', wistiaEmbed.hashedId());

                                    var formId = 'download_form_' + wistiaEmbed.hashedId();
                                    var form = $("#" + formId);
                                    if (form.length == 0) {
                                        form = $('<form id="' + formId + '" method="POST" action="' + downloadURL + '">' + options.formToken + '</form>');
                                        form.css('visible', 'hidden');
                                        $('body').append(form);
                                    }

                                    form.submit();
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

                            resize = function() {
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
                    button.addClass('off');
                }
                container.append(button);

                // Extra-controls - Autoplay
                var button = $('<div>')
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
                                    event.target.removeClass('off');
                                    $(wistiaEmbed).trigger('autoplayenabled');
                                } else {
                                    event.target.addClass('off');
                                    $(wistiaEmbed).trigger('autoplaydisabled');
                                }
                            }
                        })
                    });
                if (wistiaEmbed.options.autoPlay === false) {
                    button.addClass('off');
                }
                container.append(button);

                // Extra-controls - Focus
                var button = $('<div>')
                    .attr('id', wistiaEmbed.uuid + '_focus_button')
                    .addClass('wistia_button focus')
                    .attr('title', 'Focus')
                    .text('Focus')
                    .on('click', function(event) {
                        var url = options.focus.url;

                        var ajax = new Request({
                            method      : 'get',
                            asynchronous: 'true',
                            url         : url,
                            data        : {
                                'do': '1'
                            },
                            onSuccess   : function(result) {
                                var patt = /wistiaFocusState:([0-1])/;
                                var state = patt.exec(result);

                                if (state) {
                                    state = state[1];

                                    if (state >= 0) {
                                        if (state == 1) {
                                            event.target.removeClass('off');
                                            wistiaEmbed.plugin['dimthelights'].dim();
                                            $(wistiaEmbed).trigger('focusenabled');
                                            wistiaEmbed.params.focus = true;
                                        } else {
                                            event.target.addClass('off');
                                            wistiaEmbed.plugin['dimthelights'].undim();
                                            $(wistiaEmbed).trigger('focusdisabled');
                                            wistiaEmbed.params.focus = false;
                                        }
                                    }
                                }
                            }
                        }).send();
                    });

                if (wistiaEmbed.options.focus === false) {
                    button.addClass('off');
                }
                container.append(button);

                // Move the navigation buttons
                $(wistiaEmbed.grid.top_inside).append($('#course-navigation'));

                // Events
                var gridMain = $(wistiaEmbed.grid.main);

                function hideWistiaButtons() {
                    var buttons = $('.wistia_button, #course-navigation');

                    buttons.removeClass('visible');
                    buttons.addClass('hidden');
                }

                function showWistiaButtons() {
                    var buttons = $('.wistia_button, #course-navigation');

                    buttons.addClass('visible');
                    buttons.removeClass('hidden');
                }

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

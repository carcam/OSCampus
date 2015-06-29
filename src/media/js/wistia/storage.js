
(function($) {
    var player = $('.wistia_embed');

    if (player.length > 0) {

        // Fix the fullscreen video
        var fixFullscreen = function() {
            var oldButton = $('[id^=wistia_fullscreenButton_]'),
                newButton = oldButton.clone(),
                mainWrapper = $('#guru_content');

            newButton.addClass('custom');
            oldButton.after(newButton);
            newButton.css('width', '50px');
            newButton.css('height', '32px');
            newButton.css('right', '0');
            oldButton.remove();

            wistiaEmbed.fullscreenButton = newButton[0];
            // Disable the native fullscreen mode
            wistiaEmbed.requestFullscreen = function() {
                newButton.trigger('click');
            };

            newButton.on('click', function() {
                if (screenfull.enabled) {
                    if (screenfull.isFullscreen) {
                        screenfull.exit();
                    } else {
                        // Enable full screen only for the video and controls
                        screenfull.request($('main')[0]);
                    }
                }
            });

            wistiaEmbed.grid.center.addEventListener('mouseenter', function(e) {
                newButton.removeClass('wistia_romulus_hidden');
                newButton.addClass('wistia_romulus_visible');
            });

            wistiaEmbed.grid.center.addEventListener('mouseleave', function(e) {
                newButton.removeClass('wistia_romulus_visible');
                newButton.addClass('wistia_romulus_hidden');
            });

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
        };

        wistiaEmbed.ready(function() {
            fixFullscreen();
        });

        setNextPreviousButtonEvents = function() {
            function onClickButton(event) {
                event.preventDefault();
                var $delegateTarget = $(event.delegateTarget),
                    $guruContent = $('#guru_content'),
                    url = $delegateTarget.attr('href'),
                    isNext = $delegateTarget.attr('id') == 'nextbut',
                    loadingMsg = '';

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
            };

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
        };

        wistiaEmbed.ready(function() {
            setNextPreviousButtonEvents();
        });
    }
    ;

})(jQuery);

(function($) {
    $.extend($.fn, {
        formatSeconds: function(totalSeconds) {
            var minutes = parseInt(totalSeconds / 60, 10),
                seconds = totalSeconds - (minutes * 60),
                pad2    = function(n) {
                    if (n < 10) {
                        return '0' + n;
                    }
                    return n;
                }


            this.html(pad2(minutes) + ':' + pad2(seconds));

            return this;
        }
    });

    $.Oscampus = $.extend({}, $.Oscampus);

    $.Oscampus.quiz = {
        options: {
            timer: {
                selector: '#oscampus-timer .osc-clock',
                minutes : 99
            }
        },

        init: function(options) {
            options = $.extend(true, {}, this.options, options);

            this.timer(options.timer);

        },

        timer: function(options) {
            options = $.extend({}, this.options.timer, options);

            var clock   = $(options.selector),
                seconds = options.minutes * 60;

            clock.formatSeconds(seconds);
            var update = function() {
                seconds--;
                clock.formatSeconds(seconds);
                if (seconds == 0) {
                    clearInterval(update);
                    update = null;
                    alert('time\'s up!');
                }
            };

            var countDown = setInterval(update, 1000);
        }
    };

})(jQuery);

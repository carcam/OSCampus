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
                minutes : 10
            }
        },

        /**
         * Start the countdown timer
         *
         * @param {object} options
         */
        timer: function(options) {
            options = $.extend({}, this.options.timer, options);

            var clock   = $(options.selector),
                seconds = options.minutes * 60;

            clock.formatSeconds(seconds);
            var update = setInterval(function() {
                seconds--;
                clock.formatSeconds(seconds);
                if (seconds <= 0) {
                    clearInterval(update);
                    alert('time\'s up!');
                }
            }, 1000);
        }
    };
})(jQuery);

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
                selector  : '#oscampus-timer .osc-clock',
                timeLimit : 600,
                limitAlert: 60
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
                seconds = $.Oscampus.cookie.get('quiz_time', options.timeLimit);

            clock.formatSeconds(seconds);

            var update = setInterval(function() {
                seconds--;
                $.Oscampus.cookie.set('quiz_time', seconds);
                clock.formatSeconds(seconds);
                if (seconds <= 0) {
                    clearInterval(update);
                    $.Oscampus.cookie.delete('quiz_time');
                    alert('time\'s up!');
                }
            }, 1000);
        }
    };
})(jQuery);

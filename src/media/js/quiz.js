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
            form: '#formQuiz',
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
            options = $.extend(true, {}, this.options, options);

            var clock   = $(options.timer.selector),
                seconds = $.Oscampus.cookie.get('quiz_time', options.timer.timeLimit),
                form = $(options.form);

            clock.formatSeconds(seconds);

            var update = setInterval(function() {
                seconds--;
                $.Oscampus.cookie.set('quiz_time', seconds);
                clock.formatSeconds(seconds);
                if (seconds <= 0) {
                    clearInterval(update);
                    alert(Joomla.JText._('COM_OSCAMPUS_QUIZ_TIMEOUT'));
                    form.submit();
                }
            }, 1000);

            form.on('submit', function(evt) {
                clearInterval(update);
                $(this).find('button[type=submit]').prop('disabled', true);
                $.Oscampus.cookie.delete('quiz_time');
            });
        }
    };
})(jQuery);

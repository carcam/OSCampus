(function($) {
    $.Oscampus = $.extend({}, $.Oscampus);

    $.Oscampus.lesson = {
        previous: null,
        current : null,
        next    : null
    };

    /**
     * Method to respond to next/prev navigation buttons.
     *
     * @param {object} lessons
     * @param {object} options
     */
    $.Oscampus.lesson.navigation = function(lessons, options) {
        this.previous = lessons.previous || null;
        this.current = lessons.current || null;
        this.next = lessons.next || null;

        // We DO want the options to be overwritten for use in submodules
        options = $.extend(true, this.navigation.options, options);

        var setLoading = function(message, title) {
            $(options.container)
                .addClass('loading')
                .html('<span class="message">' + message.replace('%s', title) + '</span>');
            document.title = Joomla.JText._('COM_OSCAMPUS_LESSON_LOADING_TITLE');
        };

        if (this.next && this.next.title) {
            var next = this.next;
            $(options.buttons.next).on('click', function(evt) {
                if (next.authorised) {
                    setLoading(Joomla.JText._('COM_OSCAMPUS_LESSON_LOADING_NEXT'), next.title);
                }
            });
        }

        if (this.previous && this.previous.title) {
            var previous = this.previous;
            $(options.buttons.previous).on('click', function(evt) {
                if (previous.authorised) {
                    setLoading(Joomla.JText._('COM_OSCAMPUS_LESSON_LOADING_PREVIOUS'), previous.title);
                }
            });
        }
    };
    $.Oscampus.lesson.navigation.options = {
        container: '#oscampus.osc-container',
        buttons  : {
            previous: '#prevbut',
            next    : '#nextbut'
        }
    }
})(jQuery);

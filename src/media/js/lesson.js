(function($) {
    $.Oscampus = $.extend({}, $.Oscampus);

    $.Oscampus.lesson = {};

    /**
     * Method to respond to next/prev navigation buttons. Also provides
     * .lesson.previous, .lesson.current, .lesson.next objects for general use
     *
     * @param {object} options
     */

    $.Oscampus.lesson.previous = null;
    $.Oscampus.lesson.current = null;
    $.Oscampus.lesson.next = null;
    $.Oscampus.lesson.navigation = function(options) {
        this.previous = options.previous || null;
        this.current = options.current || null;
        this.next = options.next || null;

        var container  = $('#oscampus.osc-container'),
            setLoading = function(message, title) {
                container
                    .addClass('loading')
                    .html('<span class="message">' + message.replace('%s', title) + '</span>');
                document.title = Joomla.JText._('COM_OSCAMPUS_LESSON_LOADING_TITLE');
            };

        if (this.next && this.next.title) {
            var next = this.next;
            $('#nextbut').on('click', function(evt) {
                setLoading(Joomla.JText._('COM_OSCAMPUS_LESSON_LOADING_NEXT'), next.title);
            });
        }

        if (this.previous && this.previous.title) {
            var previous = this.previous;
            $('#prevbut').on('click', function(evt) {
                setLoading(Joomla.JText._('COM_OSCAMPUS_LESSON_LOADING_PREVIOUS'), previous.title);
            });
        }
    };
})(jQuery);

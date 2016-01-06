(function($) {
    $.Oscampus = $.extend({}, $.Oscampus);

    $.Oscampus.lesson = {};

    /**
     *
     * @param options
     *        container : Selector for main container with all modules/lessons
     *        modules   : Selector for the module parent container
     *        lessons   : Selector for the lesson parent container
     *        reset     : Selector for a reset button
     *        handle    : Selector for cursor handles
     *        cursor    : Cursor to use for dragging animation
     */
    $.Oscampus.lesson.ordering = function(options) {
        options = $.extend(true, {}, this.ordering.options, options);

        var container = $(options.container),
            cache     = container.html(),
            reset     = $(options.reset);

        var setup = function() {
            var modules = container.find(options.modules),
                lessons = container.find(options.lessons),
                targets = modules.add(lessons);

            targets
                .sortable({
                    handle: options.handle,
                    update: function() {
                        reset.show();
                    }
                })
                .disableSelection();
            lessons.children().draggable({
                connectToSortable: lessons,
                refreshPositions : true
            });
            targets.find(options.handle).css('cursor', options.cursor);

            reset.hide();
        };
        setup();

        reset.on('click', function(evt) {
            evt.preventDefault();

            container.html(cache);
            setup();
        });
    };
    $.Oscampus.lesson.ordering.options = {
        container: '#lessons',
        modules  : 'ul.oscampus-module',
        lessons  : 'ul.oscampus-lesson',
        reset    : '.reset-lesson-order',
        handle   : '.handle',
        cursor   : 'move'
    };

    /**
     * Method to respond to next/prev navigation buttons. Also provides
     * .lesson.previous, .lesson.current, .lesson.next objects for general use
     *
     * @param {object} options
     */

    $.Oscampus.lesson = $.extend($.Oscampus.lesson, {
        previous: null,
        current : null,
        next    : null,

        navigation: function(options) {
            this.previous = options.previous || null;
            this.current = options.current || null;
            this.next = options.next || null;

            var container  = $('#oscampus.osc-container'),
                setLoading = function(message) {
                    container
                        .addClass('loading')
                        .html('<span class="message">' + message + '</span>');
                    document.title = Joomla.JText._('COM_OSCAMPUS_LESSON_LOADING_TITLE');
                };

            if (this.next && this.next.title) {
                $('#nextbut').on('click', function(evt) {
                    setLoading(Joomla.JText._('COM_OSCAMPUS_LESSON_LOADING_NEXT').sprintf(this.next.title));
                }.bind(this));
            }

            if (this.previous && this.previous.title) {
                $('#prevbut').on('click', function(evt) {
                    setLoading(Joomla.JText._('COM_OSCAMPUS_LESSON_LOADING_PREVIOUS').sprintf(this.previous.title));
                }.bind(this));
            }
        }
    });
})(jQuery);

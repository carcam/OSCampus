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
        cursor   : 'grab'
    };

    $.Oscampus.lesson.actions = function(container) {
        $(container)
            .find('.oscampus-publish')
            .css('cursor', 'pointer')
            .on('click', function(evt) {
                evt.preventDefault();

                $(this).toggleClass('fa-circle fa-circle-o');
            });

        $(container)
            .find('.oscampus-delete')
            .css('cursor', 'pointer')
            .on('click', function(evt) {
                evt.preventDefault();
                if (confirm('Under construction - confirm deleting')) {
                    alert('Under construction - will delete later :)');
                }
            });

        $(container)
            .find('.oscampus-new')
            .css('cursor', 'pointer')
            .on('click', function(evt) {
                if ($(this).hasClass('oscampus-up')) {
                    alert('Under Construction - will insert new item before this one');
                } else {
                    alert('Under Construction - will insert new item after this one');
                }
            });
    };
})(jQuery);

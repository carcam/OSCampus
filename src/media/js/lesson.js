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
     */
    $.Oscampus.lesson.ordering = function(options) {
        options = $.extend({}, this.options.ordering, options);

        var container = $(options.container),
            cache     = container.html(),
            reset     = $(options.reset);

        var setup = function() {
            var modules = container.find(options.modules),
                lessons = container.find(options.lessons);

            $.Oscampus.sortable({
                selector: modules.add(lessons),
                options: {
                    update: function() {
                        reset.show();
                    }
                }
            });
            lessons.children('li').draggable({
                connectToSortable: lessons,
                refreshPositions : true
            });
            reset.hide();
        };
        setup();

        reset.on('click', function(evt) {
            evt.preventDefault();

            container.html(cache);
            setup();
        });
    };
    $.Oscampus.lesson.options = {
        ordering: {
            container: '#lessons',
            modules  : 'ul.oscampus-module',
            lessons  : 'ul.oscampus-lesson',
            reset    : '.reset-lesson-order'
        }
    };
})(jQuery);

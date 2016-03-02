(function($) {
    $.extend($.fn, {
        pop: function() {
            var top = this.get(-1);
            this.splice(this.length - 1, 1);
            return top;
        }
    });

    $.Oscampus = $.extend({}, $.Oscampus);

    $.Oscampus.admin = $.extend(true, {}, $.Oscampus.admin);

    $.Oscampus.admin.files = {
        options: {
            container: '#file-manager',
            button   : {
                delete: '.osc-file-delete',
                add   : '.osc-file-add',
                order : '.osc-file-ordering'
            },
            path     : '.osc-file-path'
        },

        init: function(options) {
            options = $.extend(true, {}, this.options, options);

            var container = $(options.container);

            // Enable the delete buttons
            container
                .find(options.button.delete)
                .css('cursor', 'pointer')
                .on('click', function(evt) {
                    evt.preventDefault();

                    $(this).parent('li').remove();
                });

            // Create new file block
            container
                .find(options.button.add)
                .css('cursor', 'pointer')
                .on('click', function(evt) {
                    evt.preventDefault();

                    var siblings = $(this).siblings();

                    if (siblings[0]) {
                        $.Oscampus.admin.files
                            .clearBlock($(siblings[0]).clone(true))
                            .insertBefore($(this));
                    }
                });

            $(options.button.order).css('cursor', 'move');
            container.find('ul').sortable({
                handle: options.button.order,
                cancel: ''
            });
        },

        clearBlock: function(fileBlock, options) {
            options = $.extend(true, {}, this.options, options);

            fileBlock
                .find('input, textarea')
                .val('');

            fileBlock.find(options.path).html('');

            return fileBlock;
        }
    };
})(jQuery);

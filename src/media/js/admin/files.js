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
            fileBlock: '.osc-file-block',
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

                    var target = $(this).parent('li');

                    if (target.siblings().length > 0) {
                        target.remove();
                    } else {
                        $.Oscampus.admin.files.clearBlock(target);
                    }
                });

            // Create new file block
            container
                .find(options.button.add)
                .css('cursor', 'pointer')
                .on('click', function(evt) {
                    evt.preventDefault();

                    var blocks = $(container).find(options.fileBlock);

                    if (blocks[0]) {
                        var newElement = $.Oscampus.admin.files.clearBlock($(blocks[0]).clone(true));
                        $(container.find('ul')).append(newElement)
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
                .find('input, textarea, select')
                .val('');

            fileBlock.find(options.path).html('');

            return fileBlock;
        }
    };
})(jQuery);
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
                        var newElement  = $(blocks[0]).clone(true),
                            chznSelects = newElement.find('select.chzn-done');

                        $.Oscampus.admin.files.clearBlock(newElement);
                        $(container.children('ul').first()).append(newElement)

                        // Handle jui chosen selectors
                        if (chznSelects[0]) {
                            newElement.find('.chzn-container').remove();
                            chznSelects
                                .removeClass('chzn-done')
                                .show()
                                .removeData('chosen')
                                .chosen();
                        }
                    }
                });

            $(options.button.order).css('cursor', 'move');
            container
                .children('ul')
                .first()
                .sortable({
                    handle: options.button.order,
                    cancel: 'input,textarea,select,option'
                });
        },

        clearBlock: function(fileBlock, options) {
            options = $.extend(true, {}, this.options, options);

            fileBlock.find('select option').attr('selected', false);
            fileBlock.find('input, textarea, select').val('');

            // trigger event for jui chzn fields
            fileBlock.find('select.chzn-done')
                .trigger('liszt:updated') // Old version
                .trigger('chosen:updated'); // New Version


            fileBlock.find(options.path).html('');

            return fileBlock;
        }
    };
})(jQuery);

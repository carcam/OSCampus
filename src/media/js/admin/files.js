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
                        var newElement = $(blocks[0]).clone(true),
                            chznSelects = newElement.find('select.chzn-done');

                        newElement = $.Oscampus.admin.files.clearBlock(newElement);

                        // Handle jui chosen selectors
                        if (chznSelects[0]) {
                            newElement.find('.chzn-container').remove();
                            chznSelects.removeClass('chzn-done').show();

                            setTimeout(function() {
                                console.log(chznSelects);
                                chznSelects.removeData('chosen').chosen({"disable_search_threshold":10,"search_contains":true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"});
                            }, 10);
                        }

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

            fileBlock.find('select option').attr('selected', false);
            fileBlock.find('input, textarea, select').val('');

            fileBlock.find(options.path).html('');

            return fileBlock;
        }
    };
})(jQuery);

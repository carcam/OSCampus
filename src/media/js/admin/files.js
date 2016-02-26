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

    $.Oscampus.admin.files = {};

    $.Oscampus.admin.files.init = function(options) {
        options = $.extend(true, {}, this.options, options);

        // Enable the delete buttons
        $(options.container)
            .find('.osc-file-delete')
            .css('cursor', 'pointer')
            .on('click', function(evt) {
                evt.preventDefault();

                $(this).parent('li').remove();
            });

        // Create new file block
        $(options.container)
            .find('.osc-file-add')
            .css('cursor', 'pointer')
            .on('click', function(evt) {
                evt.preventDefault();

                console.log(options);

                //var siblings = $(this).parent('ul').find('li.osc-file-block');
                //if (siblings[0]) {
                    alert('Under Construction');
                //}
            });

        // init movable blocks
        var container = $('.osc-file-manager');

    };
    $.Oscampus.admin.files.options = {
        container: '#file-manager'
    };
})(jQuery);

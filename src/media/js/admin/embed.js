(function($) {
    $.Oscampus = $.extend({}, $.Oscampus);

    $.Oscampus.admin = $.extend(true, {}, $.Oscampus.admin);

    $.Oscampus.admin.embed = {
        options: {
            selector: '.osc-url-embed-field'
        },

        init: function(options) {
            options = $.extend(true, {}, this.options, options);

            var fields = $(options.selector);

            fields.each(function(i, el) {
                var btn     = $('#' + el.id + '_btn'),
                    preview = $('#' + el.id + '_preview');

                btn.on('click', function(evt) {
                    preview.append('<p>' + $(el).val() + '</p>');
                });
            });
        }
    };
})(jQuery);

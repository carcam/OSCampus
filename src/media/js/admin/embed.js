(function($) {
    $.Oscampus = $.extend({}, $.Oscampus);

    $.Oscampus.admin = $.extend(true, {}, $.Oscampus.admin);

    $.Oscampus.admin.embed = {
        options: {
            urlbase : '',
            selector: '.osc-url-embed-field',
            token   : null
        },

        init: function(options) {
            options = $.extend(true, {}, this.options, options);

            var fields = $(options.selector);

            fields.each(function(i, el) {
                var btn     = $('#' + el.id + '_btn'),
                    preview = $('#' + el.id + '_preview'),
                    target = $(el)

                btn.on('click', function(evt) {
                    if (target.val()) {
                        preview.html('Loading...');

                        var data   = {
                            option: 'com_oscampus',
                            task  : 'embed.content',
                            format: 'raw',
                            url   : target.val()
                        };

                        if (options.token) {
                            data[options.token] = 1;
                        }

                        $.get({
                            url    : options.urlbase + 'index.php',
                            data   : data,
                            success: function(text, status, xhr) {
                                console.log(text);
                                preview.html(text);
                            }
                            ,
                            error  : function(error, status, xhr) {
                                preview.html(error.statusText);
                            }
                        });
                    } else {
                        preview.html('');
                    }
                });

                if (target.val()) {
                    btn.trigger('click');
                } else {
                    preview.html('');
                }
            });
        }
    };
})(jQuery);

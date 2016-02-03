(function($) {
    $.Oscampus = $.extend({}, $.Oscampus);

    $.Oscampus.admin = $.extend(true, {}, $.Oscampus.admin);

    $.Oscampus.admin.quiz = {};

    $.Oscampus.admin.quiz.init = function() {
        $('[class*=osc-add-],[class*=osc-delete-]')
            .css('cursor', 'pointer')
            .on('click', function(evt) {
                evt.preventDefault();
                alert('Under Construction');
            });
    };
})(jQuery);

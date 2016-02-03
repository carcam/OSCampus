(function($) {
    $.Oscampus = $.extend({}, $.Oscampus);

    $.Oscampus.admin = $.extend(true, {}, $.Oscampus.admin);

    $.Oscampus.admin.quiz = {};

    $.Oscampus.admin.quiz.init = function() {
        $('[class*=osc-delete-]')
            .css('cursor', 'pointer')
            .on('click', function(evt) {
                evt.preventDefault();

                $(this).parent('li').remove();
            });

        $('[class*=osc-add-answer]')
            .css('cursor', 'pointer')
            .on('click', function(evt) {
                evt.preventDefault();

                var siblings = $(this).parent('ul').find('li.osc-answer');

                if (siblings[0]) {
                    var newElement = $(siblings[0]).clone(true);

                    newElement.find('input[type=radio]').prop('checked', false);

                    var newInput = newElement.find('input[type=text]'),
                        newName = newInput.prop('name').replace(/\[\d+\]$/, '[' + siblings.length + ']'),
                        newId = newInput.prop('id').replace(/\d+$/, siblings.length);

                    console.log(newName, newId);

                    newInput
                        .prop('name', newName)
                        .prop('id', newId)
                        .val('')

                    newElement.insertBefore($(this));
                }
            });
    };
})(jQuery);

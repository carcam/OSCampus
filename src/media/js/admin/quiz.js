(function($) {
    $.fn.pop = function() {
        var top = this.get(-1);
        this.splice(this.length - 1, 1);
        return top;
    };

    $.Oscampus = $.extend({}, $.Oscampus);

    $.Oscampus.admin = $.extend(true, {}, $.Oscampus.admin);

    $.Oscampus.admin.quiz = {};

    $.Oscampus.admin.quiz.init = function() {

        // Enable the delete buttons
        $('[class*=osc-quiz-delete-]')
            .css('cursor', 'pointer')
            .on('click', function(evt) {
                evt.preventDefault();

                var target   = $(this).parent('li'),
                    siblings = target.siblings();

                if (siblings.length > 1) {
                    target.remove();
                }
            });

        // Create new answer
        $('[class*=osc-quiz-add-answer]')
            .css('cursor', 'pointer')
            .on('click', function(evt) {
                evt.preventDefault();

                var siblings = $(this).parent('ul').find('li.osc-answer');

                if (siblings[0]) {
                    var newElement = $(siblings[0]).clone(true);

                    newElement.find('input[type=radio]')
                        .prop('checked', false)
                        .val(siblings.length);

                    var newInput = newElement.find('input[type=text]'),
                        newName  = newInput.prop('name').replace(/\[\d+\]$/, '[' + siblings.length + ']'),
                        newId    = newInput.prop('id').replace(/\d+$/, siblings.length);

                    newInput
                        .prop('name', newName)
                        .prop('id', newId)
                        .val('');

                    newElement.insertBefore($(this));
                }
            });

        // Create new question
        $('[class*=osc-quiz-add-question]')
            .css('cursor', 'pointer')
            .on('click', function(evt) {
                evt.preventDefault();

                var siblings = $(this).parent('ul').find('li.osc-question');
                if (siblings[0]) {
                    var newElement = $(siblings[0]).clone(true),
                        answers    = newElement.find('li.osc-answer');

                    while (answers.length > 1) {
                        $(answers.pop()).remove();
                    }

                    newElement.find('input[type=text]').val('');
                    newElement.find('input[type=radio]').prop('checked', false);

                    var newName, newId;
                    newElement.find('input').each(function(index, element) {
                        newName = $(element).prop('name').replace(/questions\]\[\d+/, 'questions][' + siblings.length);
                        newId = $(element).prop('id').replace(/questions_\d+/, 'questions_' + siblings.length);

                        $(element).prop('name', newName).prop('id', newId);
                    });

                    newElement.insertBefore(this);
                }
            });
    };
})(jQuery);

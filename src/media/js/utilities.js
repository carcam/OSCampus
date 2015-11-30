(function($) {
    $.extend($.fn, {
        /**
         * Simple panel toggle that will enable/disable
         * any form input fields in the container
         * with option to set focus to first element on
         * enable.
         *
         * Triggers the custom events sr.enable/sr.disable
         */
        closePanel: function(state, options) {
            options = $.extend({
                focus: true
            }, options);

            if (state) {
                $(this)
                    .hide()
                    .find(':input')
                    .attr('disabled', true)
                    .trigger('sr.disable');
            } else {
                $(this)
                    .show()
                    .find(':input')
                    .attr('disabled', false)
                    .trigger('sr.enable');

                if (options.focus) {
                    $(this).find(':input:visible').first().focus();
                }
            }
            return this;
        },

        /**
         * Simple panel slider that will enable/disable
         * any form input fields in the container
         * with option to set focus to first element on
         * enable.
         *
         * Triggers the custom events sr.enable/sr.disable
         */
        closePanelSlide: function(state, options) {
            options = $.extend({
                duration: 400,
                focus   : true
            }, options);

            if (state) {
                $(this)
                    .slideUp()
                    .find(':input')
                    .attr('disabled', true)
                    .trigger('sr.disable');
            } else {
                $(this)
                    .slideDown()
                    .find(':input')
                    .attr('disabled', false)
                    .trigger('sr.enable');

                if (options.focus) {
                    $(this).find(':input:visible').first().focus();
                }
            }
            return this;
        }
    });

    $.Oscampus = $.extend({}, $.Oscampus);

    /**
     * Simple tabs. Define tab headings with any selector
     * and include the attribute data-content with a selector
     * for the content area it controls. All tabs selected by
     * the passed selector will hide all content panels
     * except the one(s) controlled by the active tab.
     *
     * @param options
     *        selector : A jQuery selector for the tab headers
     */
    $.Oscampus.tabs = function(options) {
        options = $.extend({}, this.tabs.options, options);

        var headers = $(options.selector);
        headers
            .css('cursor', 'pointer')
            .each(function(idx, active) {
                $(this)
                    .data('contentPanel', $($(this).attr('data-content')))
                    .on('click', function(evt) {
                        headers.each(function(idx) {
                            $(this)
                                .toggleClass(options.enabled, active === this)
                                .toggleClass(options.disabled, active !== this)
                                .data('contentPanel').closePanel(active !== this, options)
                        });
                    });
            });

        // Set active panel
        if (!options.active) {
            options.active = '#' + $(headers[0]).attr('id');
        }
        $(headers.filter(options.active)).trigger('click', {focus: false});
    };
    $.Oscampus.tabs.options = {
        selector: null,
        active  : null,
        enabled : 'osc-tab-enabled',
        disabled: 'osc-tab-disabled'
    };

    /**
     * Independent sliding panels. Use any selector
     * to select one or more slide controls. Use the
     * data-content attribute to select the content
     * panels to slide Up/Down on clicking the control.
     *
     * @param options
     *        selector : a jQuery selector for the slider headers
     *        visible  : bool - initial visible state (default: false)
     */
    $.Oscampus.sliders = function(options) {
        options = $.extend({}, this.sliders.options, options);

        $(options.selector).each(function() {
            $(this)
                .css('cursor', 'pointer')
                .data('contentPanel', $($(this).attr('data-content')))
                .on('click', function(evt, options) {
                    var contentPanel = $(this).data('contentPanel');
                    contentPanel.closePanelSlide(contentPanel.is(':visible'), options);
                })
                .data('contentPanel').closePanel(!options.visible, {focus: false});
        });
    };
    $.Oscampus.sliders.options = {
        selector: null,
        visible : false
    };

    $.Oscampus.ajax = function(options) {
        options = $.extend(true, {}, this.ajax.options, options);
        $.ajax(options);
    };
    $.Oscampus.ajax.options = {
        url     : 'index.php',
        data    : {
            option: 'com_oscampus',
            format: 'json'
        },
        dataType: 'json',
        success : function(result, status, xhr) {
            alert('RESULT: ' + result);
        },
        error   : function(xhr, status, error) {
            alert(error);
        }
    };

    $.Oscampus.sortable = function(options) {
        options = $.extend({}, this.sortable.options, options);

        var selection = $(options.selector);

        selection
            .sortable()
            .disableSelection()
            .children().css(options.css);
    };
    $.Oscampus.sortable.options = {
        selector: '.oscampus-sortable',
        css     : {
            cursor: 'move'
        }
    };
})(jQuery);


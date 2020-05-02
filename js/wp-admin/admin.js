
var log = console.log ? console.log : function () {};

(function ($) {

    $(function () {

        $('#project-type-layout').each(function () {
            var $t = $(this),
                $options = $('#project-type-grid-portfolio-options');

            $t.on('change keydown', function () {
                $t.val() == 'grid' ? $options.show() : $options.hide();
            }).change();
        });

    });

})(jQuery);
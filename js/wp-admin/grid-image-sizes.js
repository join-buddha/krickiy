(function ($) {

$(function () {

    var $portfolio = $('.portfolio-grid'),
        grid       = $portfolio.data('grid'),
        maxSize    = $portfolio.data($portfolio.data('orientation') == 'horizontal' ? 'rows' : 'columns');


    // Show "Click to change size" message on project hover
    $('.grid-project .inner').each(function () {
        $(this).append('<span class="note">' + wpVars['clickToChangeSize'] + '</span>')
    });


    // Clicking on project should change it's size, if the maximum size is reached, the size should go back to the smallest.
    $('.grid-project .preview').click(function (e) {
        e && e.preventDefault();

        var $el = $(this),
            $project = $el.closest('.grid-project'),
            size = $project.data('size');

        size = size < maxSize ? size + 1 : 1;

        $project.data('size', size);

        grid.render({
            force: true
        });
    });


    // Close the window and populate input field on parent window.
    $('.fluxus-customize-note .btn-save').click(function (e) {
        e && e.preventDefault();

        var data = [];

        $('.grid-project').each(function () {
            var $el = $(this);

            data.push({
                id: $el.data('id'),
                size: $el.data('size')
            });
        });

        window.opener.jQuery('[name="fluxus_portfolio_grid_image_sizes"]').val(JSON.stringify(data));
        window.close();
    });


    $('.fluxus-customize-note .btn-cancel').click(function (e) {
        e && e.preventDefault();

        window.close();
    });

});

}(jQuery));
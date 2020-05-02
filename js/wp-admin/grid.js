(function ($) {

  $(function () {

    var $aspectRatioSelect   = $('select[name="fluxus_portfolio_grid_aspect_ratio"]'),
        $autoAspectRatioNote = $('.fluxus-meta-field-aspect-ratio .notes'),
        $layout              = $('.button-grid-layout'),
        $buttonReset         = $('.button-grid-layout-reset'),
        updateNotesVisibility;

    updateNotesVisibility = function () {

      if ($aspectRatioSelect.val() == 'auto') {
        $autoAspectRatioNote.show();
      } else {
        $autoAspectRatioNote.hide();
      }

    };

    updateNotesVisibility();
    $aspectRatioSelect.on('change keyup', updateNotesVisibility);

    $layout.click(function (e) {
      e && e.preventDefault();

      var $el = $(this);
      window.open($el.attr('href'), 'layoutWindow');
    });

    $buttonReset.click(function (e) {
      e && e.preventDefault();

      var $el = $(this);

      if ( window.confirm( $el.data('confirm') ) ) {
        $('input[name="fluxus_portfolio_grid_image_sizes"]').val('');
      }
    });

  });

}(jQuery));
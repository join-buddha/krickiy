(function ($, window) {

    $(function () {

        var $postFormatRadio = $('#post-formats-select [name="post_format"]');

        if ($postFormatRadio.length) {

            var $allBoxes = $();

            $postFormatRadio.each(function () {
                $allBoxes = $allBoxes.add($('#fluxus_' + $( this ).val() + '_meta_box'));
            });

            $allBoxes.hide();

            $postFormatRadio.change( function () {
                var $t = $(this);
                if ($t.is(':checked')) {
                    $allBoxes.hide();
                    $('#fluxus_' + $t.val() + '_meta_box').show();
                }

            }).change();

        }

    });

})(jQuery, window);
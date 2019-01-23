(function ($) {
  Drupal.behaviors.ajaxBlock = {
    attach: function (context) {
      var $ajaxBlocks = $('[data-ajax-block]', context);
      if($ajaxBlocks.length > 0){
        $ajaxBlocks.each(function(index, elem){
          var $block = $(elem);
          var block_id = $block.data('dataAjaxBlock');
          var target_id = $block.attr('id');

          var ajaxSettings = {
            url: '/ajax_block/get',
            base: target_id,
            submit: {
              block_id: block_id,
              target_id: target_id
            }
          };

          Drupal.ajax(ajaxSettings).execute();
        });
      }
    }
  };
})(jQuery);
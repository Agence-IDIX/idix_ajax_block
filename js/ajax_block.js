(function ($) {
  Drupal.behaviors.ajaxBlock = {
    attach: function (context) {
      var $ajaxBlocks = $('[data-ajax-block]', context);
      if($ajaxBlocks.length > 0){
        $ajaxBlocks.each(function(index, elem){
          var $block = $(elem);
          var block_id = $block.data('ajaxBlock');
          var target_id = $block.attr('id');
          var parameters = $block.attr('parameters');

          var ajaxSettings = {
            url: '/ajax_block/get',
            base: target_id,
            submit: {
              block_id: block_id,
              target_id: target_id,
              parameters: parameters
            }
          };

          var ajaxResult = Drupal.ajax(ajaxSettings).execute();
          ajaxResult.always(function () {
            // We execute Drupal.ajax.execute manually
            // so we have to execute attachBehaviors manually
            setTimeout(function () {
              // @warn : pay attention to libraries attached to the new content and JS files particularly
              // make sure JS dependencies is loaded before calling `Drupal.attachBehaviors`
              // @info : https://www.drupal.org/project/cdn/issues/2714155
              // @info : https://www.drupal.org/project/drupal/issues/1988968
              Drupal.attachBehaviors();
            });
          });
        });
      }
    }
  };
})(jQuery);

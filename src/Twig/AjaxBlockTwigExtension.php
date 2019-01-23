<?php

namespace Drupal\idix_ajax_block\Twig;

class AjaxBlockTwigExtension extends \Twig_Extension {

  public function getName(){
    return 'idix_ajax_block.twigextension';
  }

  public function getFunctions(){
    return [
      new \Twig_SimpleFunction('ajax_block_placeholder', [$this, 'getBlockPlaceholder']),
    ];
  }

  public static function getBlockPlaceholder($block_id){
    $id = 'ajax-block--' . \Drupal::service('pathauto.alias_cleaner')->cleanString($block_id) . '--' . time();
    return [
      '#type' => 'container',
      '#attributes' => [
        'id' => $id,
        'data-ajax-block' => $block_id,
      ],
      '#attached' => [
        'library' => ['idix_ajax_block/ajax_block']
      ]
    ];
  }

}
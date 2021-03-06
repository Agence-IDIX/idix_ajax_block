<?php

namespace Drupal\idix_ajax_block\Controller;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Plugin\ContextAwarePluginInterface;
use Drupal\Core\Render\Element;
use Symfony\Component\HttpFoundation\Request;

class AjaxController extends ControllerBase {

  public function ajaxGetBlock(Request $request){
    $configuration = ['label_display' => 'hidden'];

    $id = $request->request->get('block_id');
    $target_id = $request->request->get('target_id');
    $configuration['configuration'] = unserialize($request->request->get('parameters'));

    /** @var \Drupal\Core\Block\BlockPluginInterface $block_plugin */
    $block_plugin = \Drupal::service('plugin.manager.block')
      ->createInstance($id, $configuration);

    // Inject runtime contexts.
    if ($block_plugin instanceof ContextAwarePluginInterface) {
      $contexts = \Drupal::service('context.repository')->getRuntimeContexts($block_plugin->getContextMapping());
      \Drupal::service('context.handler')->applyContextMapping($block_plugin, $contexts);
    }

    if (!$block_plugin->access(\Drupal::currentUser())) {
      $build = [];
    } else {
      $content = $block_plugin->build();

      if ($content && !Element::isEmpty($content)) {
        $build = $content;
      } else {
        // Preserve cache metadata of empty blocks.
        $build = [
          '#markup' => '',
          '#cache' => isset($content['#cache']) ? $content['#cache'] : [],
        ];
      }

      if (!empty($content)) {
        CacheableMetadata::createFromRenderArray($build)
          ->merge(CacheableMetadata::createFromRenderArray($content))
          ->applyTo($build);
      }
    }


    $response = new AjaxResponse();
    $response->addCommand(new ReplaceCommand('#' . $target_id, $build));

    return $response;
  }

}
<?php

namespace Drupal\idix_ajax_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockManager;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Provides a 'Ajax' block.
 *
 * @Block(
 *  id = "idix_ajax_block",
 *  admin_label = @Translation("IDIX Ajax Block"),
 * )
 */
class IdixAjaxBlock extends BlockBase implements BlockPluginInterface  {

  public function buildConfigurationForm(array $form, FormStateInterface $form_state)
  {
    $form = parent::buildConfigurationForm($form, $form_state);

    $config = $this->getConfiguration();

    /** @var BlockManager $blockManager */
    $blockManager = \Drupal::service('plugin.manager.block');
    $contextRepository = \Drupal::service('context.repository');

    $definitions = $blockManager->getDefinitionsForContexts($contextRepository->getAvailableContexts());

    $options = [];
    foreach($definitions as $key => $def){
      if($key != 'idix_ajax_block'){
        $options[$key] = is_string($def['admin_label']) ? $def['admin_label'] : $def['admin_label']->__toString();
      }
    }

    $sub_block_id = isset($config['sub_block_id']) ? $config['sub_block_id'] : '';

    $form['sub_block_id'] = [
      '#type' => 'select',
      '#title' => 'Block à charger',
      '#options' => $options,
      '#default_value' => $sub_block_id,
    ];

    $form['sub_block_config'] = [
      '#type' => 'container',
      '#id' => 'sub_block_config_container',
      '#attributes' => [
        'id' => 'sub_block_config_container'
      ],
    ];

    return $form;
  }

  public function submitConfigurationForm(array &$form, FormStateInterface $form_state)
  {
    $sub_block_id = $form_state->getValue('sub_block_id');
    $this->configuration['sub_block_id'] = $sub_block_id;

    parent::submitConfigurationForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $sub_block_id = $this->configuration['sub_block_id'];
    if(!empty($sub_block_id)) {
      $id = 'ajax-block--' . \Drupal::service('pathauto.alias_cleaner')->cleanString($sub_block_id) . '--' . time();
      return [
        '#type' => 'container',
        '#attributes' => [
          'id' => $id,
          'data-ajax-block' => $sub_block_id,
        ],
        '#attached' => [
          'library' => ['idix_ajax_block/ajax_block']
        ]
      ];
    }
    return [];
  }

}
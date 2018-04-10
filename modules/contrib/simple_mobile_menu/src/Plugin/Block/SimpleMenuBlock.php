<?php

namespace Drupal\simple_mobile_menu\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;

/**
 * Provides a Simple Mobile Menu block.
 *
 * @Block(
 *   id = "SimpleMenu_block",
 *   admin_label = @Translation("Simple Mobile Menu Block"),
 * )
 */

class SimpleMenuBlock extends BlockBase implements BlockPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $simple_mobile_menu_config = \Drupal::config('simple_mobile_menu.settings')->get('menu_name');
    $block_config = [];
    $block_config['menu_name'][] = $simple_mobile_menu_config;
    $block_config                = ['menu_name', $simple_mobile_menu_config];
    // Get menu tree.
    $tree = simple_mobile_menu_build_tree($block_config);
    // Build menu class.
    $tree = $this->buildMenuStyle($tree, $block_config);
    $library   = [];
    $library[] = 'simple_mobile_menu/simple_mobile_menu';
    return [ '#theme'       => 'simple_mobile_menu',
             '#attached'    => [ 'library' => $library ],
             '#menu_output' => drupal_render($tree) ];
  }
 /**
   * // Add 'menuparent' class.
   *
   * @param $items
   *
   * @return mixed
   */

  public function buildSubMenuMenuparent($items) {
    foreach ($items as $k => &$item) {
      if ( $item['below'] ) {
        $item['attributes']->addClass('has-child');
        $item['below'] = $this->buildSubMenuMenuparent($item['below']);
      }
    }
    return $items;
  }

  /**
   * Add class to smm menus.
   * @param $tree
   * @param $block_config
   * @return mixed
   */
  public function buildMenuStyle($tree, $block_config) {
    // Add default class.
    $tree['#attributes']['class'][] = 'mobile_menu';
    // Add 'menuparent' class.
    $tree['#items'] = $this->buildSubMenuMenuparent($tree['#items']);
    return $tree;
  }

}

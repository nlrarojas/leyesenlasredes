<?php

namespace Drupal\bid\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'BlockConfirmationTrucker' block.
 *
 * @Block(
 *  id = "block_confirmation_trucker",
 *  admin_label = @Translation("Block Confirmation Trucker"),
 * )
 */
class BlockConfirmationTrucker extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    $build = [];
    $nid_carga = \Drupal::routeMatch()->getParameter('node');


    if (isset($_GET['nominado'])) {

      // Rendering the Form.
      $build['trucker_form']['#markup'] = render(\Drupal::formBuilder()
        ->getForm('\Drupal\bid\Form\FormConfirmationTrucker'));
    }
    else {
      // Rendering the view.
      $bid = views_embed_view('cargas_bids', 'block_1', $nid_carga->id());
      $build['view']['#markup'] = render($bid);
    }

    return $build;
  }
}

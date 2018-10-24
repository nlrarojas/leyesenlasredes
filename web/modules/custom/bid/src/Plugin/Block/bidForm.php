<?php

namespace Drupal\bid\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;
use Drupal\Core\Session\AccountProxyInterface;

/**
 * Provides a 'bidForm' block.
 *
 * @Block(
 *  id = "bid_form",
 *  admin_label = @Translation("Bid form"),
 * )
 */
class bidForm extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $cargaNode = \Drupal::routeMatch()->getParameter('node');

    if (isset( $cargaNode ) && $cargaNode->getType() == 'carga') {
      $currentStateCarga = $this->getTheCurrentModerationState($cargaNode);
      $currentStateOferta = null;
      $build['carga_status_title']['#markup'] = "Mi Oferta";
      if ($cargaNode->get('field_carga_ofert')->getValue() !== array()) {
        $ofertaId = $cargaNode->get('field_carga_ofert')->first()->getValue()['target_id'];
        $nodeOferta = Node::load($ofertaId);
        $currentStateOferta = $this->getTheCurrentModerationState($nodeOferta);
      }

      $user = \Drupal::currentUser();
      $status = $cargaNode->get("status")->value;

      if( $status == 1) {
        // Check if user already submitted a bid.
        $bid_user = views_get_view_result('mis_ofertas_workflows', 'default', $user->id(), $cargaNode->id());

        if (count($bid_user)) {
          if (isset($_GET['op']) && $_GET['op'] == 'edit') {
            $n = Node::load($bid_user[0]->_entity->id());
            $form = $this->_bid_node_form('oferta', $cargaNode->id(), $n);
            $formRender = \Drupal::formBuilder()->getForm($form);
            $build['bid_form']['#markup'] = render($formRender);
          } else {
            //Get the user author carga
            $uid = $cargaNode->getOwnerId();
            // Check if user already has a company and phone info.
            $profileInfo = views_get_view_result('get_profile_info', 'default', $uid);
            foreach ($profileInfo as $current_profile){
                if ($current_profile->_entity->hasField('field_prof_comp_empresa')) {
                    $fieldProfCompEmpresa = $current_profile->_entity->get('field_prof_comp_empresa')->value;
                }
                if ($current_profile->_entity->hasField('field_prof_user_telefono')) {
                    $fieldProfUserTelefono = $current_profile->_entity->get('field_prof_user_telefono')->value;
                }
                if ($current_profile->_entity->hasField('field_prof_user_apellido')) {
                    $fieldProfUserApellido = $current_profile->_entity->get('field_prof_user_apellido')->value;
                }
                if ($current_profile->_entity->hasField('field_prof_user_nombre')) {
                  $fieldProfUserNombre = $current_profile->_entity->get('field_prof_user_nombre')->value;
              }
            }

            if ($currentStateCarga == 'carga_nominada' && $currentStateOferta == 'oferta_nominada') {
              //Obtiene la información de la oferta.
              $ofertaId = $cargaNode->get('field_carga_ofert')->first()->getValue()['target_id'];
              $nodeOferta = Node::load($ofertaId);
              //Obtiene el id del transportista que hizo la oferta.
              $ofertaUserId = explode('-', $nodeOferta->title->value)[0];

              $build['oferta_precio']['#markup'] = $nodeOferta->get('field_ofert_precio')->value;

              //Compara que la oferta corresponda a el usuario que la realizó.
              $user = \Drupal::currentUser();
              if ($ofertaUserId == $user->id()){
                $build['bid_form_status']['#markup'] = '<p><b>Estado: Esperando confirmación de transportista</b></p>'. '<p><b>Embarcador: '.$fieldProfCompEmpresa .'</b></p>' . '<p class="whatsapp-icon">' . $fieldProfUserTelefono.'</p>';
                $nominationAceptForm = \Drupal::formBuilder()->getForm('\Drupal\bid\Form\FormAcceptNominationTrucker');
                $build['bid_form']['#markup'] = render($nominationAceptForm);
                $build['carga_status_title']['#markup'] = "Oferta aceptada";
              } else {
                $build['bid_form_status']['#markup'] = '<p><b>Estado: Esperando confirmación del transportista</b></p>';
                $build['carga_status_title']['#markup'] = "Oferta aceptada";
              }
            } else if ($currentStateCarga == 'carga_aceptada' && $currentStateOferta == 'oferta_aceptada') {
              ///Add user ofert id to relate to the one that is accepted.
              if ($ofertaUserId == $user->id()){
                $build['bid_form_status']['#markup'] = '<p><b>Estado: Confirmada</b></p><p><b>Contacto: '.$fieldProfUserNombre.' '.$fieldProfUserApellido.'</b></p><p><b>Embarcador: '.$fieldProfCompEmpresa .'</b></p>' . '<p class="whatsapp-icon">' . $fieldProfUserTelefono.'</p>';
                $build['carga_status_title']['#markup'] = "Oferta confirmada";
              } else {
                $build['carga_status_title']['#markup'] = "Oferta confirmada a otro transportista";
                $build['bid_form_status']['#markup'] = '<p><b>Estado: Confirmada a otro transportista</b></p>';
              }
              //$build['bid_form_status']['#markup'] = '<p><b>Estado: Confirmada</b></p><p><b>Contacto: '.$fieldProfUserNombre.' '.$fieldProfUserApellido.'</b></p><p><b>Embarcador: '.$fieldProfCompEmpresa .'</b></p>' . '<p class="whatsapp-icon">' . $fieldProfUserTelefono.'</p>';
              //$build['carga_status_title']['#markup'] = "Oferta confirmada";
            } else {
              $ofertaNode = Node::load($bid_user[0]->_entity->id());
              $checkStateOferta = $this->getTheCurrentModerationState($ofertaNode);

              if ($checkStateOferta == 'oferta_rechazada' || $checkStateOferta == 'oferta_expirada' ) {
                $form = $this->_bid_node_form('oferta', $cargaNode->id());
                $build['bid_form']['#markup'] = render(\Drupal::formBuilder()->getForm($form));
                $build['carga_status_title']['#markup'] = "Oferta rechazada";
              } else {
                $bid = views_embed_view('mis_ofertas_workflows', 'default', $user->id(), $cargaNode->id());
                $build['view']['#markup'] = render($bid);

                $currentOptionForm = \Drupal::formBuilder()->getForm('\Drupal\bid\Form\CurrentOptionsForm');
                $build['bid_form']['#markup'] = render($currentOptionForm);
              }
            }
          }
        } else {
          $form = $this->_bid_node_form('oferta', $cargaNode->id());
          $formCargaNode = \Drupal::formBuilder()->getForm($form);
          $build['bid_form']['#markup'] = render($formCargaNode);
        }
      } else {
        $term = taxonomy_term_load($status[0]['target_id']);
        $build['bid_form']['#markup'] = '<div><span>Estado:</span>'.$term->get('name')->value.'</div>';
      }
    } else {
      $build['bid_form']['#markup'] = 'This block is only visible in carga node view';
    }
    $build['bid_form']['#cache']['max-age'] = 0;
    return $build;
  }

  public function _bid_node_form($type, $cid, $node = NULL) {
    $form = NULL;
    if ($node == NULL) {
      $values = ['type' => $type, 'field_ofert_carga' => $cid];
      $node = \Drupal::entityTypeManager()
        ->getStorage('node')
        ->create($values);
    }
    $form = \Drupal::entityTypeManager()
      ->getFormObject('node', 'default')
      ->setEntity($node);
    return $form;
  }

  /**
   * @param $entity
   *
   * @return null
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   */
  private function getTheCurrentModerationState($entity) {
    // Check if we are viewing the default revision.
    if ($entity->isDefaultRevision() == TRUE) {

      // Get all of the revision ids.
      $revision_ids = \Drupal::entityTypeManager()->getStorage('node')->revisionIds($entity);
      //$current_revision_id = $node->getRevisionId();

      // Check if the last item in the revisions is the loaded one.
      $last_revision_id = end($revision_ids);

      if ($entity->getRevisionId() != $last_revision_id) {
        // Load the revision.
        $last_revision = \Drupal::entityTypeManager()->getStorage('node')->loadRevision($last_revision_id);
        // Get the revisions moderation state.
        return $last_revision->get('moderation_state')->getString();
      }
    } else {
      return null;
    }
  }
}

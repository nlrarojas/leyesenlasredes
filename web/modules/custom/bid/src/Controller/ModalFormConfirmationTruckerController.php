<?php

namespace Drupal\bid\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;


class ModalFormConfirmationTruckerController extends ControllerBase {

  /**
   * @param \Symfony\Component\HttpFoundation\Request $request
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function confirmTrucker(Request $request) {

    if ($request->isXmlHttpRequest()) {
      $response = new JsonResponse();

      $cargaID = $request->request->get('carga');
      $ofertaId = $request->request->get('nominado');

      $node_bid = Node::load($cargaID);

      $trucker = $this->getTruckerUser($ofertaId);

      $nodeOferta = Node::load($ofertaId);

      //Get the user author oferta
      $transportista_id = $nodeOferta->getOwnerId();
      $transportista_profile = $current_user = User::load($transportista_id);
      // Check if user already has a company and phone info.
      $profileInfo = views_get_view_result('get_profile_info', 'default', $transportista_id);

      $fieldProfCompEmpresa = '';
      $fieldPriceOfert = $nodeOferta->get('field_ofert_precio')->value;

      foreach ($profileInfo as $current_profile){
          if ($current_profile->_entity->hasField('field_prof_comp_empresa')){
              $fieldProfCompEmpresa = $current_profile->_entity->get('field_prof_comp_empresa')->value;
          }
      }

      if ($trucker) {
        drupal_set_message("La oferta de ".$fieldProfCompEmpresa." por $".$fieldPriceOfert." ha sido aceptada. El transportista confirmara que puede realizar el flete en menos de 24 horas");
        $this->update_node($node_bid, $ofertaId);
      }

      return $response;
    }
  }

  /**
   * @param \Drupal\node\Entity\Node $node
   * @param $ofertaId
   */
  public function update_node(Node $node, $ofertaId){

    $node->field_carga_ofert = $ofertaId;
    $node->save();

    // Published is carga_abierta state
    if ($node->get('moderation_state')->value == 'published'){
      $new_state = "carga_nominada";
      $node->set('moderation_state', $new_state);
      $node->save();
    }
  }

  /**
   * @param $nominado
   *
   * @return bool|\Drupal\Core\Entity\EntityInterface|null|static
   */
  function getTruckerUser($nominado) {

    $trucker = FALSE;
    $id_bid = $nominado;
    $node_bid = Node::load($id_bid);

    if (!is_null($node_bid)) {
      $uid = $node_bid->getOwnerId();
      $user = User::load($uid);
      if (!is_null($user)) {
        $trucker = $user;
      }
    }

    // Published is oferta_enviada state
    if ($node_bid->get('moderation_state')->value == 'published'){
      $new_state = "oferta_nominada";
      $node_bid->set('moderation_state', $new_state);
      $node_bid->save();
    }

    return $trucker;
  }


  /**
   * @param \Symfony\Component\HttpFoundation\Request $request
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function acceptNominationTrucker(Request $request) {

    if ($request->isXmlHttpRequest()) {
      $response = new JsonResponse();

      $cargaID = $request->request->get('carga');
      $nodeCarga = Node::load($cargaID);
      $ofertaId = $nodeCarga->get('field_carga_ofert')->first()->getValue()['target_id'];
      $nodeOferta = Node::load($ofertaId);

      $trucker = $this->getTruckerUser($ofertaId);

      $ofertList = views_get_view_result('ofertas_de_cargas', 'block_2', $cargaID);
      foreach ($ofertList as $ofert) {
        $ofertListId = $ofert->_entity->id();
        if ($ofertListId != $ofertaId) {
          $ofertNode = Node::load($ofertListId);
          $ofertNode->get('moderation_state')->value = 'oferta_no_nominada';
          $ofertNode->save();
        }
      }

      dump($nodeCarga);
      dump($nodeCarga->get('field_carga_ofert'));

      if ($trucker) {
        //$this->update_node($node_bid, $ofertaId);
        // Published is carga_abierta state


        if ($nodeCarga->get('moderation_state')->value == 'published'){
          $new_state = "carga_aceptada";
          $nodeCarga->set('moderation_state', $new_state);
          $nodeCarga->save();
        }

        if ($nodeOferta->get('moderation_state')->value == 'oferta_nominada'){
          $new_state = "oferta_aceptada";
          $nodeOferta->set('moderation_state', $new_state);
          $nodeOferta->save();
        }

        drupal_set_message("Usted ha confirmado la oferta al embarcador. Puede contactarlo para coordinar el flete. ");
      }

      return $response;
    }
  }

  /**
   * @param \Symfony\Component\HttpFoundation\Request $request
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function rejectNominationTrucker(Request $request) {

    if ($request->isXmlHttpRequest()) {
      $response = new JsonResponse();

      $cargaID = $request->request->get('carga');
      $nodeCarga = Node::load($cargaID);
      $ofertaId = $nodeCarga->get('field_carga_ofert')->first()->getValue()['target_id'];
      $nodeOferta = Node::load($ofertaId);

      $trucker = $this->getTruckerUser($ofertaId);

      $ofertList = views_get_view_result('ofertas_de_cargas', 'block_2', $cargaID);
      foreach ($ofertList as $ofert) {
        $ofertListId = $ofert->_entity->id();
        if ($ofertListId != $ofertaId) {
          $ofertNode = Node::load($ofertListId);
          $ofertNode->get('moderation_state')->value = 'oferta_nominada';
          $ofertNode->save();
        }
      }

      if ($trucker) {
        //$this->update_node($node_bid, $ofertaId);
        // Published is carga_abierta state
        // Remove the entity reference
        $nodeCarga->field_carga_ofert = array();
        $nodeCarga->save();

        if ($nodeCarga->get('moderation_state')->value == 'published'){
          $new_state = "published";
          $nodeCarga->set('moderation_state', $new_state);
          $nodeCarga->save();
        }

        if ($nodeOferta->get('moderation_state')->value == 'oferta_nominada'){
          $new_state = "oferta_rechazada";
          $nodeOferta->get('moderation_state')->value = $new_state;
          //$nodeOferta->set('moderation_state', $new_state);
          $nodeOferta->save();
        }

        drupal_set_message("Usted ha rechazado la aceptacion de la oferta al embarcador.");
      }

      return $response;
    }
  }
}
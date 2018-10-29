<?php

namespace Drupal\bid\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;
use Drupal\taxonomy\Entity\Term;

class ModalFormConfirmationTruckerController extends ControllerBase {

  /**
   * @param \Symfony\Component\HttpFoundation\Request $request
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function confirmTrucker(Request $request) {
    if ($request->isXmlHttpRequest()) {
      $response = new JsonResponse();

      $topicID = $request->request->get('carga');
      $ofertaId = null;

      $topic = Term::load($topicID);

      $uid = \Drupal::currentUser()->id();
      $user = User::load($uid);

      /*$topics = views_get_view_result('mis_temas_de_interes', 'page_1', $uid);
      $isTopicAdded = false;
      foreach ($topics as $topic) {
        if ($topic->get('field_tema')->getValue() == $topicID) {
          $isTopicAdded = true;
        }
      }

      if (!$isTopicAdded) {*/
        $node = Node::create([
          'type' => 'mis_temas_de_interes',
          'title' => $topic->getName(),        
          'field_tema' => $topic,
          'field_usuario_tema' => $user,
        ]);  
        $node->save();
        drupal_set_message("Ahora estas siguiente el tema: " . $topic->getName());
      //}   

      //drupal_set_message("El tema: " . $topic->getName() . " ya está en sus lista de temas.");
      
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

      $topicID = $request->request->get('carga');
      $nodeTopic = Node::load($topicID);
      $nodeTopic->delete();
      
      drupal_set_message("Has dejado de seguir el tema: " . $nodeTopic->title());

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
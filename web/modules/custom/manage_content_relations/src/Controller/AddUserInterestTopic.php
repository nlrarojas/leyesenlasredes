<?php

namespace Drupal\manage_content_relations\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;


class AddUserInterestTopic extends ControllerBase {

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addTopic(Request $request) {
         if ($request->isXmlHttpRequest()) {
            $response = new JsonResponse();
            $topicID = $request->request->get('topic');
            /*
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
            */
            return $response;
        }
    }

    /**
     * @param \Drupal\node\Entity\Node $node
     * @param $ofertaId
     * @return void
     */
    public function update_node(Node $node, $ofertaId) {
    /*
        $node->field_carga_ofert = $ofertaId;
        $node->save();

        // Published is carga_abierta state
        if ($node->get('moderation_state')->value == 'published'){
          $new_state = "carga_nominada";
          $node->set('moderation_state', $new_state);
          $node->save();
        }*/
    }
}
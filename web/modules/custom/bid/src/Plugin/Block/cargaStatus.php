<?php

namespace Drupal\bid\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;
use Drupal\Core\Session\AccountProxyInterface;

/**
 * Provides a 'cargaStatus' block.
 *
 * @Block(
 *  id = "carga_status",
 *  admin_label = @Translation("Estado de la Carga"),
 * )
 */

class cargaStatus extends BlockBase {
    /**
     * {@inheritdoc}
     */
    public function build() {
        $build = [];
        $cargaNode = \Drupal::routeMatch()->getParameter('node');

        if (isset( $cargaNode ) && $cargaNode->getType() == 'carga') {
            $currentStateCarga = $this->getTheCurrentModerationState($cargaNode);
            $currentStateOferta = null;

            $fieldProfCompEmpresa = "";
            $fieldProfUserName = "";
            $fieldProfUserTelefono = "";

            if ($cargaNode->get('field_carga_ofert')->getValue() !== array()) {
                $ofertaId = $cargaNode->get('field_carga_ofert')->first()->getValue()['target_id'];
                $nodeOferta = Node::load($ofertaId);

                //Get the user author oferta
                $transportista_id = $nodeOferta->getOwnerId();
                $transportista_profile = $current_user = User::load($transportista_id);
                // Check if user already has a company and phone info.
                $profileInfo = views_get_view_result('get_profile_info', 'default', $transportista_id);

                $count = sizeof($profileInfo);

                foreach ($profileInfo as $current_profile){
                    if ($current_profile->_entity->hasField('field_prof_comp_empresa')){
                        $fieldProfCompEmpresa = $current_profile->_entity->get('field_prof_comp_empresa')->value;
                    }
                    if ($current_profile->_entity->hasField('field_prof_user_nombre') && $current_profile->_entity->hasField('field_prof_user_apellido')) {
                        $fieldProfUserName = $current_profile->_entity->get('field_prof_user_nombre')->value . " ".
                            $current_profile->_entity->get('field_prof_user_apellido')->value;
                    }
                    if ($current_profile->_entity->hasField('field_prof_user_telefono')){
                        $fieldProfUserTelefono = $current_profile->_entity->get('field_prof_user_telefono')->value;
                    }
                }
            }

            $user = \Drupal::currentUser();
            $status = $cargaNode->get("status")->value;

            if( $status == 1) {
                // Check if user already submitted a bid.
                //$bid_user = views_get_view_result('mis_ofertas_workflows', 'default', $user->id(), $cargaNode->id());
                if ($currentStateCarga == null) {
                    $build['carga_status']['#markup'] = '<p><b>Estado: Sin transportista aceptado</b></p>';
                    $build['carga_status_title']['#markup'] = "Carga abierta";
                } elseif ($currentStateCarga == 'carga_nominada') {
                    $build['carga_status']['#markup'] = '<p><b>Estado: Esperando confirmación de transportista</b></p><p><b>Transportista: </b>'.$fieldProfUserName .'</p>'.'<p><b>Empresa: </b>'.$fieldProfCompEmpresa .'</p>'.'<p class="whatsapp-icon">' . $fieldProfUserTelefono.'</p>';
                    //Changing "Carga nominada" to "Carga aceptada"
                    $build['carga_status_title']['#markup'] = "Carga aceptada";
                } elseif ($currentStateCarga == "carga_aceptada") {
                    $build['carga_status']['#markup'] = '<p><b>Estado:</b> Confirmada por transportista</p><p><b>Transportista: </b> '.$fieldProfUserName .'</p>'.'<p><b>Empresa: </b>'.$fieldProfCompEmpresa .'</p>'.'<p class="whatsapp-icon">' . $fieldProfUserTelefono.'</p>';
                    //Changing label "Carga aceptada" to "Carga confirmada"
                    $build['carga_status_title']['#markup'] = "Carga confirmada";
                } elseif ($currentStateCarga == "carga_cerrada") {
                    $build['carga_status']['#markup'] = '<p><b>Estado:</b> Sin transportista aceptado</p>';
                    $build['carga_status_title']['#markup'] = "Carga cerrada";
                }
            }
        } else {
            $build['carga_status']['#markup'] = 'This block is only visible in carga node view';
        }

        $build['carga_status']['#cache']['max-age'] = 0;
        return $build;
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
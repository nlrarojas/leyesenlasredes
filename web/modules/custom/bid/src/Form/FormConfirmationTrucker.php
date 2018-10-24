<?php

namespace Drupal\bid\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class FormConfirmationTrucker.
 */
class FormConfirmationTrucker extends FormBase {


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'form_confirmation_trucker';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $trucker = $this->getTruckerUser();
    if ($trucker != NULL) {
      $username = $trucker->getDisplayName();
      $message_form = "Nominaras a " . $trucker . " para manejar este flete.";

      $form['header'] = [
        '#markup' => '<p>' . $message_form . '</p>',
      ];

      $form['submit'] = [
        '#type' => 'submit',
        '#value' => $this->t('Confirmar'),
      ];

      $form['cancel'] = array(
        '#type' => 'submit',
        '#value' => $this->t('Cancelar'),
      );
    }else {
      $message_form = "Nominaras a " . "Vacio" . " para manejar este flete.";
      $form['header'] = [
        '#markup' => '<p>' . $message_form . '</p>',
      ];

      $form['submit'] = [
        '#type' => 'submit',
        '#value' => $this->t('Confirmar'),
      ];

      $form['cancel'] = array(
        '#type' => 'submit',
        '#value' => $this->t('Cancelar'),
      );
    }
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $node_bid = \Drupal::routeMatch()->getParameter('node');
    $nid = $node_bid->id();

    $alias = \Drupal::service('path.alias_manager')->getAliasByPath('/node/'.$nid);

    $submit_value = $form_state->getValue('op')->getUntranslatedString();

    if ($submit_value ==  'Confirmar') {

      $trucker = $this->getTruckerUser();

      if ($trucker) {
        drupal_set_message("La oferta ha sido nominada a ". $trucker->getDisplayName());
        $this->update_node($node_bid, $trucker->id());
      }
    }

    $redirect = new RedirectResponse($alias);
    $redirect->send();

  }

  public function update_node(Node $node, $author_id){

    $id_closed_awarded = 2;

    $node->field_carga_ofert = $author_id;
    $node->field_carga_status = $id_closed_awarded;
    $node->save();

  }

  function getTruckerUser(){

    $trucker = FALSE;

    if (isset($_GET['nominado'])) {

      $id_bid = $_GET['nominado'];
      $node_bid = Node::load($id_bid);

      if (!is_null($node_bid)) {
        $uid = $node_bid->getOwnerId();
        $user = User::load($uid);
        if (!is_null($user)) {
          $trucker = $user;
        }
      }
    }
    return $trucker;
  }

}

<?php

namespace Drupal\bid\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\node\Entity\Node;

/**
 * Class CurrentOptionsForm.
 */
class CurrentOptionsForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'current_options_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    if ( isset($_GET['op']) && $_GET['op'] == 'remove' ) {
      $form['header'] = [
        '#markup' => '<p>Esta oferta ser&aacute; retirada. Desea continuar?</p>',
      ];
      $form['remove'] = [
        '#type' => 'submit',
        '#attributes' => array('class' => array('btn-danger','btn-confirmar_retirar_oferta')),
        '#value' => 'Confirmar',
        '#markup' => '<p><p>'
      ];
      $current_path = \Drupal::service('path.current')->getPath();
      $form['footer'] = [
        '#markup' => '<a href="'.$current_path.'">' . t("Cancelar") . '</a>',
      ];
    } else {
      $form['edit'] = [
        '#type' => 'submit',
        '#attributes' => array('class' => array('btn-warning', 'btn-editar_oferta')),
        '#value' => 'Editar oferta',
        '#markup' => '<p><p>'
      ];
      $form['cancel'] = array(
        '#type' => 'submit',
        '#attributes' => array('class' => array('btn-link', 'btn-retirar_oferta')),
        '#value' => 'Retirar oferta'
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
    $current_path = \Drupal::service('path.current')->getPath();
    $parameter = '';

    if ($form_state->getValue('op') == 'Editar oferta') {
      $parameter = '?op=edit';
    } elseif ($form_state->getValue('op') == 'Retirar oferta') {
      $parameter = '?op=remove';
    } elseif ($form_state->getValue('op') == 'Confirmar') {
      $user = \Drupal::currentUser();
      /** @var \Drupal\node\Entity\Node $carga */
      $carga = \Drupal::routeMatch()->getParameter('node');
      $bid = views_get_view_result('mis_ofertas_workflows', 'page_1', $user->id(), $carga->id());
      $n = Node::load($bid[0]->_entity->nid->value);
      $n->delete();
      drupal_set_message("La oferta a " . $carga->getTitle() . " ha sido retirada.");
    }
    $redirect = new RedirectResponse($current_path . $parameter);
    $redirect->send();
  }
}

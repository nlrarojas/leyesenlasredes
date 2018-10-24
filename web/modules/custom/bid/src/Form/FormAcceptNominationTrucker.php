<?php
/**
 * Created by PhpStorm.
 * User: merlin
 * Date: 6/14/18
 * Time: 10:05 PM
 */

namespace Drupal\bid\Form;


use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;

class FormAcceptNominationTrucker extends FormBase {

  /**
   * Returns a unique string identifying the form.
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    // TODO: Implement getFormId() method.
    return 'form_accept_nomination_trucker';
  }

  /**
   * Form constructor.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   The form structure.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // TODO: Implement buildForm() method.
    $cargaNode = \Drupal::routeMatch()->getParameter('node');
    $uid = $cargaNode->getOwnerId();
    // Check if user already has a company and phone info.
    $profileInfo = views_get_view_result('get_profile_info', 'default', $uid);
    foreach ($profileInfo as $current_profile){
        if ($current_profile->_entity->hasField('field_prof_comp_empresa')) {
            $fieldProfCompEmpresa = $current_profile->_entity->get('field_prof_comp_empresa')->value;
        }
    }

    $user = User::load($uid);
    $question = t('¿Desea confirmar la oferta al embarcador?');
    $content = t('Ha sido nominado por ' . $fieldProfCompEmpresa .
      ' para transportar la carga #' . $cargaNode->id() . ' desde ' .
      $cargaNode->get('field_carga_ori_name')->value . ' hasta ' .
      $cargaNode->get('field_carga_des_name')->value . '.<br>Confirme que puede realizar este flete de acuerdo a su oferta.');

    $form['edit'] = [
      '#type' => 'submit',
      '#attributes' => array(
        'class' => array('btn-warning', 'btn-accept-nomination-trucker'),
        'data-carga' => $cargaNode->id(),
        'data-title' => $question,
        'title' => $question,
        'data-content' => $content->render()),
      '#value' => t('Confirmar oferta'),
      '#markup' => '<p><p>'
    ];
    return $form;
  }

  /**
   * Form submission handler.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // TODO: Implement submitForm() method.
  }
}
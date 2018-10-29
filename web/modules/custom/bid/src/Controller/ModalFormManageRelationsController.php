<?php

namespace Drupal\bid\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;
use Drupal\taxonomy\Entity\Term;

class ModalFormManageRelationsController extends ControllerBase {

  /**
   * @param \Symfony\Component\HttpFoundation\Request $request
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function followTopic(Request $request) {
    if ($request->isXmlHttpRequest()) {
      $response = new JsonResponse();

      $topicID = $request->request->get('topic');

      $topicNode = Term::load($topicID);

      $uid = \Drupal::currentUser()->id();
      $user = User::load($uid);

      $topicList = views_get_view_result('mis_temas_de_interes', 'page_1', $uid);
      $isTopicAdded = false;
      foreach ($topicList as $topic) {
        $topicListID = $topic->_entity->get('field_tema')->getValue()[0]['target_id'];
        if ($topicListID == $topicID) {
          $isTopicAdded = true;
          break;
        }
      }

      if (!$isTopicAdded) {
        $node = Node::create([
          'type' => 'mis_temas_de_interes',
          'title' => $topicNode->getName(),
          'field_tema' => $topicNode,
          'field_usuario_tema' => $user,
        ]);  
        $node->save();
        drupal_set_message("Ahora estas siguiente el tema " . $topic->getName());
      } else {
        drupal_set_message("Ya sigues el tema " . $topic->getName());
      }

      return $response;
    }
  }


  /**
   * @param \Symfony\Component\HttpFoundation\Request $request
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function unfollowTopic(Request $request) {

    if ($request->isXmlHttpRequest()) {
      $response = new JsonResponse();

      $topicID = $request->request->get('topic');
      $nodeTopic = Node::load($topicID);
      $nodeTopic->delete();
      
      drupal_set_message("Has dejado de seguir el tema: " . $nodeTopic->title());

      return $response;
    }
  }

  public function removeProject (Request $request) {
    if ($request->isXmlHttpRequest()) {
      $response = new JsonResponse();

      $topicID = $request->request->get('project');
      $nodeTopic = Node::load($topicID);
      $nodeTopic->delete();

      drupal_set_message("Has dejado de seguir el proyecto " . $nodeTopic->title());

      return $response;
    }
  }


  /**
   * @param \Symfony\Component\HttpFoundation\Request $request
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function followProject(Request $request) {
    if ($request->isXmlHttpRequest()) {
      $response = new JsonResponse();

      $projectID = $request->request->get('project');
      $projectNode = Node::load($projectID);
            
      $uid = \Drupal::currentUser()->id();
      $user = User::load($uid);

      $projectAlreadyAdded = false;
      $projectList = views_get_view_result('proyectos_seguidos', 'page_1', $uid);
      foreach ($projectList as $project) {
        $projectListId = $project->_entity->get('field_proyecto_seguido')->getValue()[0]['target_id'];
        if ($projectListId == $projectID) {
          $projectAlreadyAdded = true;
          break;
        }
      }

      if (!$projectAlreadyAdded) {
        $node = Node::create([
          'type' => 'proyecto_seguido',
          'title' => $projectNode->getTitle(),
          'field_proyecto_seguido' => $projectNode,
          'field_usuario' => $user,
        ]);  
        $node->save();
        drupal_set_message("Ahora estas siguiente el proyecto " . $projectNode->title());
      } else {
        drupal_set_message("Ya sigues el proyecto " . $projectNode->title());
      }

      return $response;
    }
  }
}
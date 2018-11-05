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
        $array = array("response" => 1);
        $node = Node::create([
          'type' => 'mis_temas_de_interes',
          'title' => $topicNode->getName(),
          'field_tema' => $topicNode,
          'field_usuario_tema' => $user,
        ]);
        $node->save();        
      } else {
        $array = array("response" => 0);        
      }
      $response = new JsonResponse($array, 200, ['Content-Type'=> 'application/json']);
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
        $array = array("response" => 1);
        $node = Node::create([
          'type' => 'proyecto_seguido',
          'title' => $projectNode->getTitle(),
          'field_proyecto_seguido' => $projectNode,
          'field_usuario' => $user,
        ]);
        $node->save();        
      } else { 
        $array = array("response" => 0);
      }      
      $response = new JsonResponse($array, 200, ['Content-Type'=> 'application/json']);
      return $response;
    }
  }

  /**
   * @param \Symfony\Component\HttpFoundation\Request $request
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function openVoting(Request $request) {
    if ($request->isXmlHttpRequest()) {      
      $projectID = $request->request->get('project');
      $projectNode = Node::load($projectID);

      $status = Term::load(3);

      $projectAlreadyAdded = false;
      $projectList = views_get_view_result('votacion_proyectos', 'block_1');
      foreach ($projectList as $project) {
        $projectListId = $project->_entity->get('field_proyecto')->getValue()[0]['target_id'];
        if ($projectListId == $projectID) {
          $projectAlreadyAdded = true;
          $project->_entity->set('field_estado_votacion', $status);
          $project->_entity->save();
          break;
        }
      }

      if (!$projectAlreadyAdded) {
        $array = array("response" => 1);
        $node = Node::create([
          'type' => 'votacion_proyecto_de_ley',
          'title' => $projectNode->getTitle(),
          'field_estado_votacion' => $status,
          'field_proyecto' => $projectNode,
          'field_votos_a_favor' => 0,
          'field_votos_en_contra' => 0,
        ]);
        $node->save();        
      } else {
        $array = array("response" => 1);
      }
      $response = new JsonResponse($array, 200, ['Content-Type'=> 'application/json']);
      return $response;
    }
  }

/**
   * @param \Symfony\Component\HttpFoundation\Request $request
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function closeVoting(Request $request) {
    if ($request->isXmlHttpRequest()) {      
      $projectID = $request->request->get('project');
      $projectNode = Node::load($projectID);

      $projectList = views_get_view_result('votacion_proyectos', 'block_1');
      foreach ($projectList as $project) {
        $projectListId = $project->_entity->get('field_proyecto')->getValue()[0]['target_id'];
        if ($projectListId == $projectID) {
          $status = Term::load(8);
          $project->_entity->set('field_estado_votacion', $status);
          $project->_entity->save();
          break;
        }
      }

      $array = array("response" => 1);
      $response = new JsonResponse($array, 200, ['Content-Type'=> 'application/json']);
      return $response;
    }
  }

  /**
   * @param \Symfony\Component\HttpFoundation\Request $request
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function voteUp(Request $request) {
    if ($request->isXmlHttpRequest()) {      
      $projectID = $request->request->get('project');
      $projectVote = Node::load($projectID);

      $uid = \Drupal::currentUser()->id();
      $userNode = User::load($uid);

      $votesView = views_get_view_result('voto_usuario_proyecto', 'default', $projectID, $uid);

      if (count($votesView) > 0) {
        $idProjectVote = $votesView[0]->_entity->get('field_voto_proyecto')->getValue()[0]['target_id'];
        $projectVote = Node::load($idProjectVote);

        $votesFavor = $projectVote->get('field_votos_a_favor')->value;
        $votesNonFavor = $projectVote->get('field_votos_en_contra')->value;

        $array = array("response" => 0, "votesFavor" => $votesFavor, "votesNonFavor" => $votesNonFavor);
      } else {
        $node = Node::create([
          'type' => 'voto',
          'title' => 'Voto ',
          'field_voto' => true,
          'field_voto_estado' => true,
          'field_voto_proyecto' => $projectVote,
          'field_voto_usuario' => $userNode,
        ]);
        $votesFavor = $projectVote->get('field_votos_a_favor')->value;
        $votesNonFavor = $projectVote->get('field_votos_en_contra')->value;

        $projectVote->set('field_votos_a_favor', $votesFavor + 1);
        $array = array("response" => 1, "votesFavor" => $votesFavor + 1, "votesNonFavor" => $votesNonFavor);

        $projectVote->save();
        $node->save();
      }
      
      $response = new JsonResponse($array, 200, ['Content-Type'=> 'application/json']);
      return $response;
    }
  }

  /**
   * @param \Symfony\Component\HttpFoundation\Request $request
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function voteDown(Request $request) {
    if ($request->isXmlHttpRequest()) {
      $projectID = $request->request->get('project');
      $projectVote = Node::load($projectID);

      $uid = \Drupal::currentUser()->id();
      $userNode = User::load($uid);

      $votesView = views_get_view_result('voto_usuario_proyecto', 'default', $projectID, $uid);

      if (count($votesView) > 0) {
        $idProjectVote = $votesView[0]->_entity->get('field_voto_proyecto')->getValue()[0]['target_id'];
        $projectVote = Node::load($idProjectVote);

        $votesFavor = $projectVote->get('field_votos_a_favor')->value;
        $votesNonFavor = $projectVote->get('field_votos_en_contra')->value;

        $array = array("response" => 0, "votesFavor" => $votesFavor, "votesNonFavor" => $votesNonFavor);
      } else {
        $node = Node::create([
          'type' => 'voto',
          'title' => 'Voto ',
          'field_voto' => false,
          'field_voto_estado' => true,
          'field_voto_proyecto' => $projectVote,
          'field_voto_usuario' => $userNode,
        ]);
        $votesFavor = $projectVote->get('field_votos_a_favor')->value;
        $votesNonFavor = $projectVote->get('field_votos_en_contra')->value;

        $projectVote->set('field_votos_en_contra', $votesFavor + 1);

        $array = array("response" => 1, "votesFavor" => $votesFavor, "votesNonFavor" => $votesNonFavor + 1);

        $projectVote->save();
        $node->save();
      }

      $response = new JsonResponse($array, 200, ['Content-Type'=> 'application/json']);
      return $response;
    }
  }
}
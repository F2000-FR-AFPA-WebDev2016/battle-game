<?php

namespace Afpa\BattleGameBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Afpa\BattleGameBundle\Model\Board;
use Afpa\BattleGameBundle\Entity\Game;
use Afpa\BattleGameBundle\Entity\User;

class GameController extends Controller {

    /**
     * @Route("/", name="game_accueil")"
     * @Template()
     */
    public function accueilAction() {
        return array();
    }

    /**
     * @Route("/game", name="game_home")
     * @Template()
     */
    public function indexAction(Request $request) {

        $session = $request->getSession();

        $oBoard = $session->get('oBoard', null);
        if (!$oBoard instanceof Board) {
            $oBoard = new Board();
            $session->set('oBoard', $oBoard);
        }
        return array();
    }

    /**
     * @Route("/game/view", name="game_view")
     * @Template()
     */
    public function gameViewAction(Request $request) {
        $session = $request->getSession();

        $oBoard = $session->get('oBoard', null);
        if (!$oBoard instanceof Board) {
            $oBoard = new Board();
            $session->set('oBoard', $oBoard);
        }


        return array(
            'board_pieces1' => $oBoard->getBoardPieces1(),
            'board_shoot1' => $oBoard->getBoardShoot1(),
            'board_pieces2' => $oBoard->getBoardPieces2(),
            'board_shoot2' => $oBoard->getBoardShoot2(),
            'player' => $oBoard->getPlayer(),
        );
    }

    /**
     * @Route("/game/action", name="game_action")
     * @Template()
     */
    public function doAction(Request $request) {
        $x = $request->get('x');
        $y = $request->get('y');

        $session = $request->getSession();
        $oBoard = $session->get('oBoard', null);
        $oBoard->doClick($x, $y);

        return new Response($x . ',' . $y);
    }

    /**
     * @Route("/game/list", name="game_list")"
     * @Template()
     */
    public function listRoomsAction(Request $request) {
        // Afficher la liste des parties existantes (= entity : Game)
        $repo = $this->getDoctrine()->getRepository('AfpaBattleGameBundle:Game');
        $aGames = $repo->findAll();

        // SI utilisateur connecté, récupérer la partie si existante
        // Et si partie démarrée (started), redirection vers game/play/[id]
        $oGame = null;

        return array(
            'games' => $aGames,
            'game_user' => $oGame,
        );
    }

    /**
     * @Route("/game/create", name="game_create")
     * @Template()
     */
    public function createGameAction(Request $request) {
        $oSession = $request->getSession();
        if (!$oSession->get('oUser') instanceof User) {
            return $this->redirect($this->generateURL('game_list'));
        }

        $oGame = new Game;
        $oGame->setData('');
        $oGame->setName('');
        $oGame->setCreatedDate(new \Datetime('now'));
        $oGame->setStatus(0);
        $oGame->setName('test4');
        $oGame->setCreatedDate(new \DateTime('now'));
        $oGame->setStatus(Game::STATUS_WAITING);
        $oGame->setData('');

        $em = $this->getDoctrine()->getManager();
        $em->persist($oGame);
        $em->flush();

        $repo = $this->getDoctrine()->getRepository('AfpaBattleGameBundle:User');
        $oUserBdd = $repo->findOneByLogin($oSession->get('oUser')->getLogin());
        $oUserBdd->setGame($oGame);
        $em->flush();

        return $this->redirect($this->generateURL('game_list'));
    }

    /**
     * @Route("/game/join/{idGame}", name="game_join")
     * @Template()
     */
    public function joinGameAction(Request $request, $idGame) {
        $oSession = $request->getSession();
        if (!$oSession->get('oUser') instanceof User) {
            return $this->redirect($this->generateURL('game_list'));
        }

        $repo = $this->getDoctrine()->getRepository('AfpaBattleGameBundle:Game');
        $oGame = $repo->find($idGame);

        if ($oGame instanceof Game) {
            $repo = $this->getDoctrine()->getRepository('AfpaBattleGameBundle:User');
            $oUserBdd = $repo->findOneByLogin($oSession->get('oUser')->getLogin());
            $oUserBdd->setGame($oGame);
            $em = $this->getDoctrine()->getManager();
            $em->flush();


            // On vérifie que la partie est complète
            if (count($oGame->getUsers()) == 2) {
                // -> Si oui, on la
                $oBoard = new Board;
                $oBoard->setPlayers($oGame);

                $oGame->setStatus(Game::STATUS_STARTED);
                $oGame->setData(serialize($oBoard));
                $em->flush();
                return $this->redirect($this->generateURL('game_play', array('idGame' => $idGame)));
            }
        }

        return $this->redirect($this->generateURL('game_list'));
    }

    /**
     * @Route("/game/play/{idGame}", name="game_play")"
     * @Template()
     */
    public function playGameAction(Request $request, $idGame) {
        $repo = $this->getDoctrine()->getRepository('AfpaBattleGameBundle:Game');
        $oGame = $repo->findOneBy(array(
            'id' => $idGame,
            'status' => Game::STATUS_STARTED
        ));

        if (!$oGame instanceof Game) {
            return $this->redirect($this->generateURL('game_list'));
        }

        return array(
            'idGame' => $idGame
        );
    }

    /**
     * @Route("/game/refresh/{idGame}", name="game_refresh")"
     * @Template()
     */
    public function refreshGameAction(Request $request, $idGame) {
        $repo = $this->getDoctrine()->getRepository('AfpaBattleGameBundle:Game');
        $oGame = $repo->findOneBy(array(
            'id' => $idGame,
            'status' => Game::STATUS_STARTED
        ));

        if (!$oGame instanceof Game) {
            return $this->redirect($this->generateURL('game_list'));
        }

        // Afficher la partie

        $oBoard = unserialize($oGame->getData());

        return array(
            'board_pieces1' => $oBoard->getBoardPieces1(),
            'board_shoot1' => $oBoard->getBoardShoot1(),
            'board_pieces2' => $oBoard->getBoardPieces2(),
            'board_shoot2' => $oBoard->getBoardShoot2(),
            'player' => $oBoard->getPlayer(),
        );
    }

}

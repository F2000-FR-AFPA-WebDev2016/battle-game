<?php

namespace Afpa\BattleGameBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Afpa\BattleGameBundle\Model\Board;
use Afpa\BattleGameBundle\Entity\Game;

class GameController extends Controller {

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
     * @Route("/game/create", name="game_create")
     * @Template()
     */
    public function gameCreateAction(Request $request) {
        // 1. Créer une entité Game (date de création)
        // 2. Associer l'utilisateur au jeu (BDD)
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

        return array('games' => $aGames);
    }

    /**
     * @Route("/", name="game_accueil")"
     * @Template()
     */
    public function accueilAction() {
        return array();
    }

    /**
     * @Route("/game/create", name="game_create")
     * @Template()
     */
    public function createGameAction(Request $request) {
        $oSession = $request->getSession();

        $oGame = new Game;
        $oGame->setName('test4');
        $oGame->setCreatedDate(new \DateTime('now'));
        $oGame->setStatus(Game::STATUS_WAITING);
        //$oGame->setTopscore(0);

        $em = $this->getDoctrine()->getManager();
        $em->persist($oGame);
        $em->flush();

        //$oGame->getId();
        $repo = $this->getDoctrine()->getRepository('AfpaBattleGameBundle:User');
        $oUserBdd = $repo->findOneByLogin($oSession->get('oUser')->getLogin());
        $oUserBdd->setGame($oGame);
        $em->flush();

        return $this->redirect($this->generateURL('game_list'));
    }

}

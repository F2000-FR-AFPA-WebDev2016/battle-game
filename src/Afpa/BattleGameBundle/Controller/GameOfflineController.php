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

class GameOfflineController extends Controller {

    /**
     * @Route("/", name="game_accueil")"
     * @Template()
     */
    public function indexAction() {
        return array();
    }

    /**
     * @Route("/game", name="game_home")
     * @Template()
     */
    public function playGameAction(Request $request) {
        $session = $request->getSession();

        $oBoard = $session->get('oBoard', null);
        if (!$oBoard instanceof Board) {
            $oBoard = new Board();
            $session->set('oBoard', $oBoard);
        }
        return array();
    }

    /**
     * @Route("/game/refresh")
     * @Template()
     */
    public function gameRefreshAction(Request $request) {
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
     * @Route("/game/action")
     * @Template()
     */
    public function doAction(Request $request) {
        $x = $request->get('x');
        $y = $request->get('y');

        $oSession = $request->getSession();
        $oBoard = $oSession->get('oBoard', null);
        $oBoard->doClick($x, $y);

        return new Response($x . ',' . $y);
    }

}

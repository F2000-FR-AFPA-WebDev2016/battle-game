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

class GameOnlineController extends Controller {

    /**
     * @Route("/game/list", name="game_list")"
     * @Template()
     */
    public function listRoomsAction(Request $request) {
        $oSession = $request->getSession();

        // Afficher la liste des parties existantes (= entity : Game)
        $repo = $this->getDoctrine()->getRepository('AfpaBattleGameBundle:Game');
        $aGames = $repo->findAll();

        // Si utilisateur connecté, récupérer la partie si existante
        // Et si partie démarrée (started), redirection vers game/play/[id]
        $oGame = null;
        if ($oSession->get('oUser') instanceof User) {
            $repo = $this->getDoctrine()->getRepository('AfpaBattleGameBundle:User');
            $oUser = $repo->find($oSession->get('oUser')->getId());

            $oGame = $oUser->getGame();
            if ($oGame instanceof Game && $oGame->getStatus() == Game::STATUS_STARTED) {
                return $this->redirect($this->generateURL('game_play', array('idGame' => $oGame->getId())));
            }
        }

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
     * @Route("/game/refresh/{idGame}")"
     * @Template()
     */
    public function gameRefreshAction(Request $request, $idGame) {
        $oSession = $request->getSession();

        $repo = $this->getDoctrine()->getRepository('AfpaBattleGameBundle:Game');
        $oGame = $repo->findOneBy(array(
            'id' => $idGame,
            'status' => Game::STATUS_STARTED
        ));

        // condition de sortie
        if (!$oGame instanceof Game) {
            return $this->redirect($this->generateURL('game_list'));
        }

        // Afficher la partie
        $oBoard = unserialize($oGame->getData());

        $idUser = null;
        if ($oSession->get('oUser') instanceof User) {
            $idUser = $oSession->get('oUser')->getId();
        }

        $aData = $oBoard->getInfosJoueur($idUser);

        $sPlayer = $oBoard->getPlayer();
        if ($oSession->get('oUser')->getId() == $oBoard->getPlayerId()) {
            $sPlayer = "C'est à vous de jouer !";
        }

        return array(
            'idGame' => $idGame,
            'player' => $sPlayer,
            'board_pieces1' => $aData['board_pieces1'],
            'board_shoot1' => $aData['board_shoot1'],
            'board_pieces2' => $aData['board_pieces2'],
            'board_shoot2' => $aData['board_shoot2'],
        );
    }

    /**
     * @Route("/game/action/{idGame}")
     * @Template()
     */
    public function doAction(Request $request, $idGame) {
        $oSession = $request->getSession();

        $repo = $this->getDoctrine()->getRepository('AfpaBattleGameBundle:Game');
        $oGame = $repo->findOneBy(array(
            'id' => $idGame,
            'status' => Game::STATUS_STARTED
        ));

        // condition de sortie : le jeu n'existe pas ou n'est pas démarré
        if (!$oGame instanceof Game) {
            return new Response();
        }

        // condition de sortie : l'utilisateur n'est pas connecté
        if (!$oSession->get('oUser') instanceof User) {
            return new Response();
        }

        $x = $request->get('x');
        $y = $request->get('y');


        $oBoard = unserialize($oGame->getData());
        $oBoard->doClick($x, $y);
        $oGame->setData(serialize($oBoard));

        // Sauvegarde du nouvel état
        $this->getDoctrine()->getManager()->flush();

        return new Response($x . ',' . $y);
    }

}

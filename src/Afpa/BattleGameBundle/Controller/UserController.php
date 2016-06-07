<?php

namespace Afpa\BattleGameBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Afpa\BattleGameBundle\Entity\User;

class UserController extends Controller {

    /**
     * @Route("/register", name="register")
     * @Template()
     */
    public function registerAction(Request $request) {
        $oUser = new User;

        $oForm = $this->createFormBuilder($oUser)
                ->add('login', 'text')
                ->add('password', 'password')
                ->getForm();

        if ($request->isMethod('POST')) {
            $oForm->bind($request);
            if ($oForm->isValid()) {
                $oUser->setName($oUser->getLogin());
                $oUser->setTopscore(0);

                //Sauvegarde utilisateur en base de données
                $em = $this->getDoctrine()->getManager();
                $em->persist($oUser);
                $em->flush();
                return $this->redirect($this->generateURL('game_accueil'));
            }
        }

        return array('form' => $oForm->createView());
    }

    /**
     * @Route("/login", name="login")
     * @Template()
     */
    public function loginAction(Request $request) {
        $oUserForm = new User;

        $oForm = $this->createFormBuilder($oUserForm)
                ->add('login', 'text')
                ->add('password', 'password')
                ->getForm();

        if ($request->isMethod('POST')) {
            $oForm->bind($request);

            if ($oForm->isValid()) {
                // Recuperation de l'utilisateur via son login
                $repo = $this->getDoctrine()->getRepository('AfpaBattleGameBundle:User');
                $oUserBdd = $repo->findOneByLogin($oUserForm->getLogin());

                // Verification de l'utilisateur via son mot de passe
                //if ($oUserBdd && $oUserBdd->getPassword() == $oUserForm->getPassword()) {
                if ($oUserBdd && $oUserBdd->verifAuth($oUserForm->getPassword())) {
                    // user valid
                    $oSession = $request->getSession();
                    $oSession->set('oUser', $oUserBdd);
                    //$_SESSION['oUser]=$oUserBdd;

                    return $this->redirect($this->generateURL('game_list'));
                }
            }
        }

        return array('form' => $oForm->createView());
    }

    /**
     * @Route("/logout", name="logout")
     * @Template()
     */
    public function logoutAction(Request $request) {
        //vider la session
        $request->getSession()->clear();
        //rediriger vers page d'accueil
        return $this->redirect($this->generateURL('game_accueil'));
    }

}

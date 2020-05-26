<?php


namespace App\Controller;

use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

class ProfileController extends AbstractController
{

    public function dashboard(UserInterface $user){
        return $this->render('views/profile.html.twig',['user'=>$user]);
    }

    public function addTag(Request $request, UserInterface $user){

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('profile');
        }

        return $this->render('views/addTag.html.twig', ['form'=>$form->createView()]);
    }
}
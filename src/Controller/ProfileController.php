<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\User\UserInterface;

class ProfileController extends AbstractController
{

    public function dashboard(UserInterface $user){
        return $this->render('views/profile.html.twig',['user'=>$user]);
    }
}
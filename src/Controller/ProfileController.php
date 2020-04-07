<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProfileController extends AbstractController
{

    public function dashboard(){
        return $this->render('views/profile.html.twig');
    }
}
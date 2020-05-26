<?php


namespace App\Controller;


use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\User\UserInterface;

class HomeController extends AbstractController
{
    public function index(UserInterface $user){
        $products = $this->getDoctrine()
            ->getRepository(Product::class)
            ->findBy(['available' => true, 'tag'=>$user->getTag()]);
        $all = $this->getDoctrine()
            ->getRepository(Product::class)
            ->findBy(['available' => true]);


        return $this->render('views/home.html.twig', ['products' => $products, 'all'=>$all]);
    }

}
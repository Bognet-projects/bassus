<?php


namespace App\Controller;


use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    public function index(){
        $products = $this->getDoctrine()
            ->getRepository(Product::class)
            ->findBy(['available' => true]);

        return $this->render('views/home.html.twig', ['products' => $products]);
    }

}
<?php


namespace App\Controller;


use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Security;

class HomeController extends AbstractController
{
    public function index(Security $security){
        $products = null;
        if ($security->getUser() != null)
        $products = $this->getDoctrine()
            ->getRepository(Product::class)
            ->findBy(['available' => true, 'tag'=>$security->getUser()->getTag()]);
        $all = $this->getDoctrine()
            ->getRepository(Product::class)
            ->findBy(['available' => true]);


        return $this->render('views/home.html.twig', ['products' => $products, 'all'=>$all]);
    }

}
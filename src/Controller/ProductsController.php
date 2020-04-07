<?php


namespace App\Controller;


use App\Entity\Product;
use App\Entity\User;
use App\Form\ProductType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

class ProductsController extends AbstractController
{

    public function edit($id, UserInterface $user, Request $request){
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $product = $this->getDoctrine()
            ->getRepository(Product::class)
            ->findOneBy(['id' => $id, 'owner' => $user->getID()]);

        if (!$product){
            return $this->redirectToRoute('profile');
        }else{
            $form = $this->createForm(ProductType::class, $product);

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()){
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($product);
                $entityManager->flush();
                return $this->redirectToRoute('profile');
            }

        }

        return $this->render('views/productView.html.twig', ['product' => $product, 'form'=>$form->createView()]);
    }

    public function show($id)
    {
        $product = $this->getDoctrine()
            ->getRepository(Product::class)
            ->findOneBy(['id' => $id]);

        return $this->render('views/productShow.html.twig', ['product' => $product]);
    }

    public function add(UserInterface $user, Request $request){
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $product = new Product();
        $owner = $this->getDoctrine()->getRepository(User::class)->find($user->getID());
        $form = $this->createForm(ProductType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $product = $form->getData();
            $product->setOwner($owner);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($product);
            $entityManager->flush();
            return $this->redirectToRoute('profile');
        }

        return $this->render('views/productView.html.twig', ['product' => $product, 'form'=>$form->createView()]);
    }
    public function delete($id, UserInterface $user){
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $entityManager = $this->getDoctrine()->getManager();
        $product = $this->getDoctrine()
            ->getRepository(Product::class)
            ->findOneBy(['id' => $id, 'owner' => $user->getID()]);

        if ($product){
            $entityManager->remove($product);
            $entityManager->flush();
        }
        return $this->redirectToRoute('profile');
    }
}
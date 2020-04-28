<?php


namespace App\Controller;


use App\Entity\Bet;
use App\Entity\Product;
use App\Entity\User;
use App\Form\BetType;
use App\Form\ProductType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
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

    public function show($id, Request $request, Security $security)
    {
        $product = $this->getDoctrine()
            ->getRepository(Product::class)
            ->findOneBy(['id' => $id]);
        $startPrice = $product->getStartPrice();
        $max = $startPrice;
        foreach ($product->getBets() as $bet){
                if($max < $bet->getPrice()){
                    $max = $bet->getPrice();
                }
        }

        $form = $this->createForm(BetType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $bet = $form->getData();
            if ($startPrice >= $bet->getPrice()){
                $this->addFlash('error', 'Nem magasabb mint a minimum összeg!');
                return $this->redirectToRoute('productShow', ['id' => $id]);
            }
            if ($max >= $bet->getPrice()){
                $this->addFlash('error', 'Nem lehet alacsonyabb mint az eddigi maximális összeg!');
                return $this->redirectToRoute('productShow', ['id' => $id]);
            }
            $bet->setProduct($product);
            $bet->setUser($security->getUser());
            $bet->setDate(new \DateTime());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($bet);
            $entityManager->flush();
            $this->addFlash('success', 'Sikeres licit tét!');
            return $this->redirectToRoute('productShow', ['id' => $id]);
        }

        return $this->render('views/productShow.html.twig', ['product' => $product, 'form' => $form->createView()]);
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
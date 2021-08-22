<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    /**
     * @Route("/cart", name="cart")
     */
    public function index(SessionInterface $session, ProductRepository $productRepository)
    {
        $cart = $session->get('cart', []);
        $cartWithData = [];

        foreach($cart as $id => $number) {
            $cartWithData[] = [
                'product' => $productRepository->find($id),
                'number' => $number
            ];
        }

        $total = 0;
        foreach($cartWithData as $item) {
            $totalItem = $item['product']->getPrice() * $item['number'];
            $total += $totalItem;
        }

        return $this->render('cart/index.html.twig', [
            'items' => $cartWithData,
            'total' => $total
        ]);
    }

    /**
     * @Route("/cart/add/{id}", name="cartAdd")
     */
    public function cartAdd(SessionInterface $session, $id){
        $cart = $session->get('cart', []);

        if(!empty($cart[$id])) {    //nếu đã có item trong cart
            $cart[$id]++; //số lượng item tăng lên
        } else {                    //nếu chưa có item trong cart
            $cart[$id] = 1;//số lượng item là 1
        }

        $session->set('cart', $cart); 
        
        return $this->redirectToRoute('cus_product');
    }

    /**
     * @Route("/cart/remove/{id}", name="cart_remove")
     */
    public function cartRemove(SessionInterface $session, $id){
        $cart = $session->get('cart', []);

        if(!empty($cart[$id])) {
            unset($cart[$id]);
        }

        $session->set('cart', $cart);
        
        return $this->redirectToRoute('cart');
    }
}

<?php

namespace App\Controller;

use App\Form\OrderType;
use App\Service\Cart\CartService;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use function PHPUnit\Framework\classHasAttribute;
use function PHPUnit\Framework\objectHasAttribute;

/**
 * PresentationController
 */
class PresentationController extends AbstractController
{
    /**
     * @Route("/")
     * @Template
     * @return     array
     */
    public function index(): array
    {
        return [];
    }

    /**
     * @Route("/presentation")
     * @Template
     * @return array
     */
    public function presentation(
        CategoryRepository $categories,
        ProductRepository $products,
        CartService $cartService
    ) {
        $id = 0;
        $AllCategories = $categories->findAll();
        $AllProducts = $products->findAllAvailable();
        return [
            'categories' => $AllCategories,
            'AllProducts' => $AllProducts,
            'items' => $cartService->getFullCart(),
            'total' => $cartService->getTotal(),
        ];
    }

    /**
     * @Route("/presentation/add/{id}", name="cart_add")
     * @return void
     */
    public function add($id, CartService $cartService)
    {
        $cartService->add($id);
        return $this->redirectToRoute('app_presentation_presentation');
    }

    /**
     * Remove a product from the cart
     * @Route("/presentation/remove/{id}", name="cart_remove")
     * @return void
     */
    public function remove($id, CartService $cartService)
    {
        $cartService->remove($id);
        return $this->redirectToRoute('app_presentation_presentation');
    }

    /**
     * Order
     * @Route("/presentation/order")
     * @Template
     */
    public function order(Request $request, CartService $cartService)
    {
        $form = $this->createForm(OrderType::class);
        $form->handleRequest($request);
        $pseudo = '';
        if ($form->isSubmitted() && $form->isValid()) {
            $forms[] = $request->request->get('order');
            $pseudo = $forms[0]['pseudo'];

            return $this->redirectToRoute('app_payment_checkout', []);
        }
        return [
            'form' => $form->createView(),
            'items' => $cartService->getFullCart(),
            'total' => $cartService->getTotal(),
        ];
    }
}

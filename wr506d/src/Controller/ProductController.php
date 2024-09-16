<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/product', name: 'product_index')]
    public function index(): Response
    {
        return $this->render('product/index.html.twig');
    }

    #[Route('/products', name: 'product_list')]
    public function listProducts(): Response
    {
        return $this->render('product/list.html.twig');
    }

    #[Route('/product/{id}', name: 'product_view')]
    public function viewProduct(Request $request, $id): Response
    {
        return $this->render('product/view.html.twig', [
            'id' => $id,
        ]);
    }
}
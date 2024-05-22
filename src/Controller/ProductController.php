<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
//Imports
use App\Entity\Product;
use App\Form\ProductFormType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductController extends AbstractController
{
    private $em;
    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }

    // Product Index
    #[Route('/product/index', name: 'product_index')]
    public function index(ProductRepository $productRepo): Response
    {
        //$products = $productRepo->findBy(['isDelete' => false]);                
        $allProducts = $productRepo->findBy(['isDelete' => false]);
        $products = [];
        foreach ($allProducts as $product) {
            $category = $product->getCategory();
            if ($category->getIsDelete() == false) {                
                $products[] =$product;
            } else {
                $product->setIsDelete(true);
            }
        }
        return $this->render('product/index.html.twig', ['products' => $products]);
    }

    // Product Create
    #[Route('/product/create', name: 'product_create')]
    public function create(Request $request, ValidatorInterface $validator,
                           ProductRepository $productRepo): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductFormType::class, $product);
        $form->handleRequest($request);
        $submittedToken = $request->request->get('token');

        if ($form->isSubmitted()) {

            if ($this->isCsrfTokenValid('generico', $submittedToken)) {

                $errors = $validator->validate($product);
                if (count($errors) > 0) {

                    $this->addFlash('mensaje', 'Ocurrió un error. Vuelve a intentar');
                    return $this->redirectToRoute('product_create');
                } else {

                    $campos = $form->getData();
                    $existe = $this->em->getRepository(Product::class)->findOneBy(['name' => $campos->getName()]);
                    if ($existe) {

                        $this->addFlash('mensaje', 'El producto ya existe');
                        return $this->redirectToRoute('product_create');
                    }
                    $productRepo->add($product, true);                    
                    $this->addFlash('mensaje', 'El producto se creo correctamente');
                    return $this->redirectToRoute('product_index');
                }
            }else {
                $this->addFlash('mensaje', 'Ocurrió un error. Vuelve a intentar');
                return $this->redirectToRoute('product_index');
            }
        }        
        return $this->render('product/create.html.twig', ['form' => $form, 'errors' => array()]);
    }

    // Product GetAll
    #[Route('/product/getAll', name: 'product_getAll')]
    public function getAll(ProductRepository $productRepo): Response
    {
        $datos = $productRepo->findAll();        

        return $this->render('product/getAll.html.twig', ['datos' => $datos]);
    }

    // Product Edit
    #[Route('/product/edit/{id}', name: 'product_edit')]
    public function edit(Product $product, ValidatorInterface $validator, Request $request,
                         ProductRepository $productRepo): Response
    {
        $form = $this->createForm(ProductFormType::class, $product);
        $form->handleRequest($request);
        $submittedToken = $request->request->get('token');

        if ($form->isSubmitted()) {

            if ($this->isCsrfTokenValid('generico', $submittedToken)) {

                $errors = $validator->validate($product);
                if (count($errors) > 0) {

                    $this->addFlash('mensaje', 'Ocurrió un error');
                    return $this->render('product/edit.html.twig', compact('form', 'errors', 'product'));
                } else {

                    $productRepo->add($product, true);
                    $this->addFlash('mensaje', 'El producto se editó correctamente');
                    return $this->redirectToRoute('product_index');
                }
            } else {                
                $this->addFlash('mensaje', 'Ocurrio un error');
                return $this->redirectToRoute('product_edit', ['id' => $product->getId()]);
            }
        }
        return $this->render('product/edit.html.twig', ['form' => $form, 'errors' => array(),
                             'product' => $product]);
    }

    // Product logical delete
    #[Route('/product/delete/{id}', name: 'product_delete')]
    public function delete(Product $product): Response
    {
        $product->setIsDelete(true);
        $this->em->flush();
        $this->addFlash('mensaje', 'El producto se eliminó correctamente');
        return $this->redirectToRoute('product_index');        
    }

    // Product data base delete
    #[Route('/product/deletedb/{id}', name: 'product_deletedb')]
    public function deletedb(Product $product): Response
    {
        $this->em->remove($product);
        $this->em->flush();
        $this->addFlash('mensaje', 'El producto se eliminó correctamente');
        return $this->redirectToRoute('product_index');        
    }

}

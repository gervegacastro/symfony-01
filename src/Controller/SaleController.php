<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
//Imports
use App\Entity\Sale;
use App\Form\SaleFormType;
use App\Entity\Item;
use App\Form\ItemFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\SaleRepository;
use App\Repository\ItemRepository;

class SaleController extends AbstractController
{

    private $em;
    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }

    // Sale Index
    #[Route('/sale/index', name: 'sale_index')]
    public function index(SaleRepository $saleRepo): Response
    {
        $datos = $saleRepo->findBy(['isDelete' => false]);        

        return $this->render('sale/index.html.twig', ['datos' => $datos]);
    }

    // Sale Create
    #[Route('/sale/create', name: 'sale_create')]
    public function create(Request $request, ValidatorInterface $validator, 
                           SaleRepository $saleRepo): Response
    {
        $sale = new Sale();
        $form = $this->createForm(SaleFormType::class, $sale);
        $form->handleRequest($request);
        $submittedToken = $request->request->get('token');

        if ($form->isSubmitted()) {

            if ($this->isCsrfTokenValid('generico', $submittedToken)) {

                $errors = $validator->validate($sale);
                if (count($errors) > 0) {

                    $this->addFlash('mensaje', 'Ocurrio un error. Vulve a intentar');
                    return $this->redirectToRoute('sale_create');
                } else {
                    $sale->setDate(new \Datetime);
                    $sale->setAmount(0);
                    // Ver nuevas functions en repo
                    $saleRepo->add($sale, true);                     
                    $this->addFlash('mensaje', 'La venta se creo correctamente');
                    return $this->redirectToRoute('item_create', ['id' => $sale->getId()]);                                        
                }
            } else {

                $this->addFlash('mensaje', 'Ocurrio un error. Vulve a intentar');
                return $this->redirectToRoute('sale_create');
            }
        }        
        return $this->render('sale/create.html.twig', ['form' => $form, 'errors' => array()]);
    }

    // Sale GetAll
    #[Route('/sale/getAll', name: 'sale_getAll')]
    public function getAll(SaleRepository $saleRepo): Response
    {
        $datos = $saleRepo->findAll();        

        return $this->render('sale/getAll.html.twig', ['datos' => $datos]);
    }

    // Sale Edit
    #[Route('/sale/edit/{id}', name: 'sale_edit')]
    public function edit(Sale $sale, ValidatorInterface $validator, Request $request,
                         SaleRepository $saleRepo): Response
    {
        $form = $this->createForm(SaleFormType::class, $sale);
        $form->handleRequest($request);
        $submittedToken = $request->request->get('token');
        
        if ($form->isSubmitted()){

            if ($this->isCsrfTokenValid('generico', $submittedToken)) {

                $errors = $validator->validate($sale);
                if (count($errors) > 0) {

                    $this->addFlash('mensaje', 'Ocurrió un error');
                    return $this->render('sale/edit.html.twig', compact('form', 'errors', 'sale'));
                } else {

                    $saleRepo->add($sale, true); 
                    $this->addFlash('mensaje', 'La venta se editó correctamente');
                    return $this->redirectToRoute('item_create', ['id' => $sale->getId()]); 
                }
            } else {

                $this->addFlash('mensaje', 'Ocurrió un error');
                return $this->redirectToRoute('sale_edit', ['id' => $sale->getId()]);
            }
        }
        return $this->render('sale/edit.html.twig', ['form' => $form, 'errors' => array(),
                             'sale' => $sale]);
    }

    // Sale Logical delete
    #[Route('/sale/delete/{id}', name: 'sale_delete')]
    public function delete(Sale $sale): Response
    {
        $sale->setIsDelete(true);
        $this->em->flush();
        $this->addFlash('mensaje', 'El producto se eliminó correctamente');
        return $this->redirectToRoute('sale_index');
    }

    // Sale Data base delete
    #[Route('/sale/deletedb/{id}', name: 'sale_deletedb')]
    public function deletedb(Sale $sale, SaleRepository $saleRepo): Response
    {
        $saleRepo->remove($sale, true);
        $this->addFlash('mensaje', 'El producto se eliminó correctamente');
        return $this->redirectToRoute('sale_index');
    }

    // Sale detail
    #[Route('/sale/detail/{id}', name: 'sale_detail')]
    public function detail(Sale $sale): Response
    {
        return $this->render('sale/detail.html.twig', ['sale' => $sale]);        
    }


    // ITEMS       

    // Item Create
    #[Route('/sale/{id}/itemCreate', name: 'item_create')]
    public function itemCreate(Request $request,Sale $sale, ValidatorInterface $validator, 
                               ItemRepository $itemRepo, SaleRepository $saleRepo): Response
    {     
                
        $item = new Item();
        $form = $this->createForm(ItemFormType::class, $item);
        $form->handleRequest($request);
        $submittedToken = $request->request->get('token');
        if ($form->isSubmitted()) {

            if ($this->isCsrfTokenValid('generico', $submittedToken)) {

                $errors = $validator->validate($item);
                if (count($errors) > 0) {
                    $this->addFlash('mensaje', 'Ocurrio un error. Vulve a intentar');
                    return $this->redirectToRoute('item_create');
                } else {
                    $item->setSale($sale);
                    $product = $item->getProducto();
                    if ($item->getProducto()->getStock() >= $item->getQuantity()) {

                        $product->setStock($product->getStock() - $item->getQuantity());
                        $item->setAmount($item->getProducto()->getPrice() * $item->getQuantity());
                        $itemRepo->add($item, true);
                        //hace suma a sale 
                        $sale->setAmount($sale->getAmount()+$item->getAmount());
                        $saleRepo->add($sale, true);
                        $this->addFlash('mensaje', 'El item se creo correctamente');                    
                        return $this->redirectToRoute('item_create',array('id'=>$item->getSale()->getId()));

                    } else {  
                        
                        $this->addFlash('mensaje', 'El stock disponible no es suficiente');                    
                        return $this->redirectToRoute('item_create',array('id'=>$item->getSale()->getId()));
                    }                    
                }
            } else {

                $this->addFlash('mensaje', 'Ocurrio un error. Vulve a intentar');
                return $this->redirectToRoute('item_create');
            }
        }        
        return $this->render('sale/item/create.html.twig', ['form' => $form, 'errors' => array(),
                             'sale' => $sale]);
    }

    // Item DB Delete
    #[Route('/sale/item/deletedb/{id}', name: 'item_deletedb')]
    public function itemDeletedb(Item $item, ItemRepository $itemRepo, SaleRepository $saleRepo): Response
    {
        $sale = $item->getSale();   
        $sale->setAmount($sale->getAmount() - $item->getAmount());
        $saleRepo->add($sale, true);     
        $itemRepo->remove($item, true);
        
        $this->addFlash('mensaje', 'El item se eliminó correctamente');
        return $this->redirectToRoute('item_create',['id' => $sale->getId()]);
    }
    
 



}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
// Imports
use App\Entity\Category;
use App\Form\CategoryFormType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CategoryController extends AbstractController
{
    private $em;
    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }

    // Category Index
    #[Route('/category/index', name: 'category_index')]
    public function index(CategoryRepository $categoryRepo): Response
    {
        $datos = $categoryRepo->findBy(['isDelete' => false]);
        
        return $this->render('category/index.html.twig', ['datos' => $datos]);
    }

    // Category Create
    #[Route('/category/create', name: 'category_create')]
    public function create(Request $request, ValidatorInterface $validator,
                           CategoryRepository $categoryRepo): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);
        $submittedToken = $request->request->get('token');

        if ($form->isSubmitted()) {

            if ($this->isCsrfTokenValid('generico', $submittedToken)) {

                $errors = $validator->validate($category);
                if (count($errors) > 0) {
                    
                    $this->addFlash('mensaje', 'Ocurrió un error. Vuelve a intentar');
                    return $this->redirectToRoute('category_create');
                } else {

                    $campos = $form->getData();
                    $existe = $this->em->getRepository(Category::class)->findOneBy(['name' => $campos->getName()]);
                    if ($existe) {

                        $this->addFlash('mensaje', 'La categoría ya existe');
                        return $this->redirectToRoute('category_create');
                    }
                    $categoryRepo->add($category, true);
                    $this->addFlash('mensaje', 'La categoría se creo correctamente');
                    return $this->redirectToRoute('category_index');
                }
            } else {

                $this->addFlash('mensaje', 'Ocurrió un error. Vuelve a intentar');
                return $this->redirectToRoute('category_index');
            }
        }
        return $this->render('category/create.html.twig', ['form' => $form, 'errors' => array()]);
    }

    // Category getAll
    #[Route('/category/getAll', name: 'category_getAll')]
    public function getAll(CategoryRepository $categoryRepo): Response
    {
        $datos = $categoryRepo->findAll();

        return $this->render('category/getAll.html.twig', ['datos' => $datos]);
    }

    // Category Edit
    #[Route('/category/edit/{id}', name: 'category_edit')]
    public function edit(Category $category, ValidatorInterface $validator, 
                         Request $request, CategoryRepository $categoryRepo): Response
    {
        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);
        $submittedToken = $request->request->get('token');

        if ($form->isSubmitted()) {

            if ($this->isCsrfTokenValid('generico', $submittedToken)) {

                $errors = $validator->validate($category);
                if (count($errors) > 0) {

                    $this->addFlash('mensaje', 'Ocurrió un error');
                    return $this->render ('category/edit.html.twig', compact('form', 'erros', 'category'));
                
                } else {
                    $categoryRepo->add($category, true);
                    $this->addFlash('mensaje', 'La categoría se editó correctamente');
                    return $this->redirectToRoute('category_index');
                }
            } else {

                $this->addFlash('mensaje', 'Ocurrió un error');
                return $this->redirectToRoute('category_edit', ['id' => $category->getId()]);
            }
        }
        return $this->render('category/edit.html.twig', ['form' => $form, 'errors' => array(),
                             'category' => $category]);        
    }

    // Category logical delete 
    #[Route('/category/delete/{id}', name: 'category_delete')]
    public function delete(Category $category): Response
    {
        $category->setIsDelete(true);        
        $this->em->flush();
        $this->addFlash('mensaje', 'La categoría se eliminó correctamente');
        return $this->redirectToRoute('category_index');
        
    }

    // Category data base delete 
    #[Route('/category/deletedb/{id}', name: 'category_deletedb')]
    public function deletedb(Category $category): Response
    {
        $this->em->remove($category);
        $this->em->flush();
        $this->addFlash('mensaje', 'La categoría se eliminó correctamente');
        return $this->redirectToRoute('category_index');        
    }    

}

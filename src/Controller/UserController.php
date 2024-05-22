<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
// Imports
use App\Entity\User;
use App\Form\UserFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends AbstractController
{
    private $em;
    public function __construct (EntityManagerInterface $em) {
        
        $this->em = $em;
    }

    // HOME
    #[Route('/user/index', name: 'user_index')]
    public function index(UserRepository $userRepo): Response
    {
        $datos = $userRepo->findBy(['isDelete' => false]); 

        return $this->render('user/index.html.twig', ['datos' => $datos]);
    }

    // LOGIN
    #[Route('/login', name: 'user_login')]
    public function userLogin(AuthenticationUtils $authentication): Response
    {
        $error = $authentication->getLastAuthenticationError();
        $lastUserName = $authentication->getLastUsername();

        return $this->render('user/login.html.twig', ['error'=>$error, 'last_username'=>$lastUserName ]);
    }

    // LOGOUT
    #[Route('/user/logout', name: 'user_logout')]
    public function userLogout()
    {        
    }

    // CREATE USER
    #[Route('/user/create', name: 'user_create')]
    public function userCreate(Request $request, ValidatorInterface $validator,
                               UserPasswordHasherInterface $userPass, UserRepository $userRepo): Response
    {              
        $user = new User(); // Creo el objeto       
        $form = $this->createForm(UserFormType::class, $user); // Creo el formulario        
        $form->handleRequest($request); // Recibo la data          
        $submittedToken = $request->request->get('token'); // Extraigo valor del token CSRF (viene de la vista).

        // Validations
        if ($form->isSubmitted()) { // si el form se envió

            if ($this->isCsrfTokenValid('generico', $submittedToken)) { // Si el token es válido

                $errors = $validator->validate($user); // Valida los campos segun las anotation de la entity
                if (count($errors) > 0) {

                    $this->addFlash('mensaje', 'Ocurrio un error. Vuelve a intentar');
                    return $this->render('user/create.html.twig', ['form'=>$form, 'errors' => array()]);

                }else {
                    // Pasadas las validaciones... 
                    $campos = $form->getData();
                    // Compruebo q el email no exista previamente
                    $existe = $this->em->getRepository(User::class)->findOneBy(['email' => $campos->getEmail()]);
                    if ($existe) {

                        $this->addFlash('mensaje', 'El email ya existe');
                        return $this->redirectToRoute('acceso_registro');
                    }
                    // Creo el registro
                    $userRepo->add($user, true);
                    //$this->addFlash('mensaje', 'El usuario se creo correctamente');
                    return $this->redirectToRoute('user_index');
                }
            } else {
                $this->addFlash('mensaje', 'Ocurrio un error. Vuelve a intentar');
                return $this->redirectToRoute('user_create');
            }
        }
        return $this->render('user/create.html.twig', ['form'=>$form, 'errors' => array()]);
    }


    // GET USER
    #[Route('/user/get', name: 'user_get')]
    public function userGet(): Response
    {
        return $this->render('user/get.html.twig');
    }

    // GET ALL USERS
    #[Route('/user/getAll', name: 'user_getAll')]
    public function userGetAll(UserRepository $userRepo): Response
    {
        $datos = $userRepo->findAll();  

        return $this->render('user/getAll.html.twig', ['datos' => $datos]);        
    }

    // EDIT USER
    #[Route('/user/edit/{id}', name: 'user_edit')]
    public function userEdit(User $user, Request $request, ValidatorInterface $validator,
                             UserPasswordHasherInterface $userPass, UserRepository $userRepo): Response
    {
        $form = $this->createForm(UserFormType::class, $user); // Creo el formulario. Uso el mismo q Create       
        $form->handleRequest($request); // Recibo la data          
        $submittedToken = $request->request->get('token'); // Extraigo valor del token CSRF (viene de la vista).

        // Validations
        if ($form->isSubmitted()) {

            if ($this->isCsrfTokenValid('generico', $submittedToken)) {

                $errors = $validator->validate($user);
                if (count($errors) > 0 ) {

                    $this->addFlash('mensaje', 'Ocurrió un error');
                    return $this->render('user/edit.html.twig', compact ('form', 'errors', 'entity'));

                } else {

                    $userRepo->add($user, true);
                    $this->addFlash('mensaje', 'El registro se modificó correctamente');
                    return $this->redirectToRoute('user_index');                    
                }
            } else {               

                $this->addFlash('mensaje', 'Ocurrio un error');
                return $this->redirectToRoute('user_edit', ['id'=>$user->getId()]);
            }
        }
        return $this->render('user/edit.html.twig', ['form'=>$form, 'errors'=>array(), 'user'=>$user]);
    }

    // Logical delete user
    #[Route('/user/delete/{id}', name: 'user_delete')]
    public function userDelete(User $user): Response
    {
        // Proceso de eliminar
        //$this->em->remove($entity);
        $user->setIsDelete(true);
        $this->em->flush();
        $this->addFlash('mensaje', 'El registro se eliminó correctamente');
        return $this->redirectToRoute('user_index');       
    }

    // Data base delete user
    #[Route('/user/deletedb/{id}', name: 'user_deletedb')]
    public function userDeletedb(User $user): Response
    {
        /*
        // Busco el registro según el id que viene por parámetro (si usara id)
        $entity = $this->em->getRepository(User::class)->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Esta url no existe');
        }*/

        // Proceso de eliminar. Con el id de la url symfony me busca automáticamente el user del param.
        $this->em->remove($user);        
        $this->em->flush();
        $this->addFlash('mensaje', 'El registro se eliminó correctamente');
        return $this->redirectToRoute('user_index');       
    }
    
}

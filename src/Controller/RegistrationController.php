<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\UserType;
use App\Security\LoginFormAuthAuthenticator;
use Doctrine\DBAL\Types\TextType;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthAuthenticator $authenticator): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/backoffice/admins", name="list_admin", methods={"GET"})
     * @return Response
     */
    public function listAdmins() :Response {
       $users = $this->getDoctrine()->getRepository(User::class)->findAll();
       $admins = [];
       $nonAdmins = [];
       foreach ($users as $admin){
           if(in_array('ROLE_ADMIN', (array)$admin->getRoles())) {
               $admins[] = $admin;
           }else{
               $nonAdmins[] = $admin;
           }
       }
       return $this->render('registration/admins.html.twig',[
           'users' => $nonAdmins,
           'admins' => $admins,
       ]);
    }

    /**
     * @Route("/backoffice/admins/promote/{id}", name="add_admin", methods={"GET"})
     * @return Response
     */
    public function promote($id){
        $manager = $this->getDoctrine()->getManager();
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        $user->addRole('ROLE_ADMIN');
        $manager->persist($user);
        $manager->flush();
        return $this->redirectToRoute('list_admin');
    }

    /**
     * @Route("/backoffice/admins/relegate/{id}", name="delete_admin", methods={"GET"})
     * @return Response
     */
    public function revoke($id){
        $manager = $this->getDoctrine()->getManager();
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        $roles = $user->getRoles();
        $roles = array_diff($roles, array("ROLE_ADMIN"));
        $user->setRoles($roles);
        $manager->persist($user);
        $manager->flush();
        return $this->redirectToRoute('list_admin');
    }
}

<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationController extends AbstractController
{
    private $passwordEncoder;

    public function __construct(UserPasswordHasherInterface $passwordEncoder, ManagerRegistry $doctrine)
    {
        $this->passwordEncoder = $passwordEncoder;

        $this->doctrine = $doctrine;
    }
    /**
     * @Route("/registration", name="app_registration")
     */
    public function index(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //hash the new users password
            $user->setPassword($this->passwordEncoder->hashPassword($user, $user->getPassword()));

            //set the Roles

            $user->setRoles(['ROLE_USER']);

            //Save data

            $em = $this->doctrine->getManager();

            $em->persist($user);

            $em->flush();


        }
        return $this->render('registration/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

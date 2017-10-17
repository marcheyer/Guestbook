<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Security\Core\SecurityContext;

class SecurityController extends Controller
{
    public function loginAction(AuthenticationUtils $authUtils)
    {
        $error = $authUtils->getLastAuthenticationError();

        $form = $this->get('form.factory')->createNamedBuilder(null)
            ->setAction($this->generateUrl('login'))
            ->add('username', TextType::class, ['label' => 'Benutzer: '])
            ->add('password', PasswordType::class, ['label' => 'Passwort: '])
            ->add('login', SubmitType::class, ['label' => 'Anmelden'])
            ->getForm();

        return $this->render('security/login.html.twig', ['form' => $form->createView(), 'error'=> $error]);
    }
}

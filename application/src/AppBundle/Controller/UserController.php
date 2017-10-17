<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * User controller.
 */
class UserController extends Controller
{
    /**
     * Lists all user entities.
     *
     * @return RedirectResponse|Response
     */
    public function indexAction()
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            $entityManager = $this->getDoctrine()->getManager();
            $users         = $entityManager->getRepository(User::class)->findAll();

            return $this->render(
                'user/index.html.twig',
                [
                    'users' => $users
                ]
            );
        } else if ($this->isGranted('ROLE_USER')) {
            $entityManager = $this->getDoctrine()->getManager();
            $userid        = $this->getUser()->getId();
            $user          = $entityManager->getRepository(User::class)->find($userid);

            return $this->render('user/index.html.twig', ['users' => [$user]]);
        }

        return $this->redirectToRoute('home');
    }

    /**
     * Creates a new user entity.
     *
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     *
     * @return RedirectResponse|Response
     */
    public function newAction(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $user = new User(["ROLE_USER"]);
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        //encode password
        $encodedPW = $encoder->encodePassword($user, $user->getPassword());
        $user->setPassword($encodedPW);


        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            //populate token storage with userpassword token
            $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
            $this->get('security.token_storage')->setToken($token);
            $this->get('session')->set('_security_main', serialize($token));

            return $this->redirectToRoute('guestbook_index');
        }

        return $this->render('user/new.html.twig', ['form' => $form->createView()]);
    }

    /**
     * Displays a form to edit an existing user entity.
     *
     * @param Request $request
     * @param User $user
     * @param UserPasswordEncoderInterface $encoder
     *
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, User $user, UserPasswordEncoderInterface $encoder)
    {
        $deleteForm = $this->createDeleteForm($user);
        $editForm   = $this->createForm('AppBundle\Form\UserType', $user);
        $editForm->handleRequest($request);

        $user->setUpdatedAt(new \DateTime());

        //encode password
        $encodedPW = $encoder->encodePassword($user, $user->getPassword());
        $user->setPassword($encodedPW);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render(
            'user/edit.html.twig',
            [
                'user'        => $user,
                'edit_form'   => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
            ]
        );
    }

    /**
     * Deletes a user entity.
     *
     * @param Request $request
     * @param User    $user
     *
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, User $user)
    {
        $user->setActive(false);
        $form = $this->createDeleteForm($user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->get('security.token_storage')->setToken(null);
            $this->get('session')->invalidate();
        }

        return $this->redirectToRoute('home');
    }

    /**
     * Creates a form to delete a user entity.
     *
     * @param User $user The user entity
     *
     * @return Form The form
     */
    private function createDeleteForm(User $user)
    {

        return $this->createFormBuilder()
            ->setAction($this->generateUrl('user_delete', ['id' => $user->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }
}

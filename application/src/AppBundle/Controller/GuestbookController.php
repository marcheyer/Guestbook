<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Guestbook;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Guestbook controller.
 */
class GuestbookController extends Controller
{
    /**
     * Lists all guestbook entities.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        /** @var ObjectManager $em */
        $em         = $this->get('doctrine')->getManager();
        $guestbooks = $em->getRepository('AppBundle:Guestbook')->findAll();
        $user       = $request->getUser();

        return $this->render(
            'guestbook/index.html.twig',
            [
                'user'       => $user,
                'guestbooks' => $guestbooks,
            ]
        );
    }

    /**
     * Creates a new guestbook entity.
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function newAction(Request $request)
    {
        //preparing Guestbook object
        $guestbook = new Guestbook();
        $user      = $this->getUser()->getUsername();
        $guestbook->setUser($user);
        $guestbook->setDate(new \DateTime());
        //preparing form
        $form      = $this->createForm('AppBundle\Form\GuestbookType', $guestbook);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($guestbook);
            $entityManager->flush();

            return $this->redirectToRoute('guestbook_index', ['id' => $guestbook->getId()]);
        }

        return $this->render(
            'guestbook/new.html.twig',
            [
                'guestbook' => $guestbook,
                'form'      => $form->createView(),
            ]
        );
    }

    /**
     * Displays a form to edit an existing guestbook entity.
     *
     * @param Request   $request
     * @param Guestbook $guestbook
     *
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, Guestbook $guestbook)
    {
        $deleteForm = $this->createDeleteForm($guestbook);
        $editForm   = $this->createForm('AppBundle\Form\GuestbookType', $guestbook);
        $editForm->handleRequest($request);

        $guestbook->setDate(new \DateTime());

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('guestbook_index', ['id' => $guestbook->getId()]);
        }

        $user = $request->getUser();
        return $this->render(
            'guestbook/edit.html.twig',
            [
                'user'        => $user,
                'guestbook'   => $guestbook,
                'edit_form'   => $editForm->createView(),
                'delete_form' => $deleteForm->createView()
            ]
        );
    }

    /**
     * Deletes a guestbook entity.
     *
     * @param Request   $request
     * @param Guestbook $guestbook
     *
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, Guestbook $guestbook)
    {
        $form = $this->createDeleteForm($guestbook);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($guestbook);
            $em->flush();
        }

        return $this->redirectToRoute('guestbook_index');
    }

    /**
     * Creates a form to delete a guestbook entity.
     *
     * @param Guestbook $guestbook
     *
     * @return Form
     */
    private function createDeleteForm(Guestbook $guestbook)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('guestbook_delete', ['id' => $guestbook->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }
}

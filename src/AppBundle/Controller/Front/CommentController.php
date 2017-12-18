<?php

namespace AppBundle\Controller\Front;

use AppBundle\Entity\Comment;
use AppBundle\Entity\Event;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CommentController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws EntityNotFoundException
     */
    public function storeAction(Request $request)
    {
        $event = $this->getDoctrine()
            ->getRepository(Event::class)
            ->find($request->request->get('event'))
        ;
        if (!$event) {
            throw new EntityNotFoundException('Event not found');
        }

        $comment = new Comment();
        $comment->setContent($request->request->get('content'));
        $comment->setPublishedAt(new \DateTime());
        $comment->setEvent($event);

        $validator = $this->get('validator');
        $errors = $validator->validate($comment);

        if (count($errors) > 0) {
            return $this->getAction($request->request->get('event'), $errors);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($comment);
        $em->flush();

        return $this->redirectToRoute('front_event_get', ['id' => $event->getId()]);
    }

    private function getAction($id, $errors)
    {
        $event = $this->getDoctrine()
            ->getRepository(Event::class)
            ->find($id)
        ;

        return $this->render('@App/event/event.html.twig', [
            'event' => $event,
            'errors' => $errors
        ]);
    }
}

<?php

namespace AppBundle\Controller\Front;

use AppBundle\Entity\Event;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    public function participateAction(Request $request)
    {
        $user = $this->getUser();

        $event = $this->getDoctrine()
            ->getRepository(Event::class)
            ->find($request->request->get('event'))
        ;

        $participants = $event->getParticipants();

        foreach ($participants as $participant) {
            if ($participant->getId() == $user->getId()) {
                return $this->render('@App/event/event.html.twig', [
                    'event' => $event,
                    'participant_error' => 'You are already a participant'
                ]);
            }
        }

        if ($user->getId() == $request->request->get('user')) {
            $event->addParticipant($user);
        }

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $this->redirectToRoute('front_event_get', ['id' => $event->getId()]);
    }
}

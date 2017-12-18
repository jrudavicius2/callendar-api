<?php

namespace AppBundle\Controller\Front;

use AppBundle\Entity\Event;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        $events = $this->getDoctrine()->getRepository(Event::class)->findAll();
        return $this->render('@App/default/index.html.twig', [
            'events' => $events
        ]);
    }

    public function authCodeAction(Request $request)
    {
        return new JsonResponse($request->query->get('code'));
    }
}

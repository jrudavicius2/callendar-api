<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Category;
use AppBundle\Entity\City;
use AppBundle\Entity\Comment;
use AppBundle\Entity\Event;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EventController extends FOSRestController
{
    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Create an event",
     *  output="AppBundle\Entity\Event",
     *     statusCodes={
     *         200="Returned when successful",
     *         400="Returned when the input parameters are bad"
     *     }
     * )
     *
     * @Rest\View
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = json_decode($request->getContent(), true);

        $user = $em->getRepository(User::class)->find($data['creator']);
        if (!$user) {
            throw new NotFoundHttpException('Creator not found');
        }

        $city = $em->getRepository(City::class)->find($data['city']);
        if (!$city) {
            throw new NotFoundHttpException('City not found');
        }

        $category = $em->getRepository(Category::class)->find($data['category']);
        if (!$category) {
            throw new NotFoundHttpException('Category not found');
        }

        $event = new Event();
        $event->setName($data['name'] ?: '');
        $event->setType($data['type'] ?: '');
        $event->setDescription($data['description'] ?: '');
        $event->setDate(new \DateTime($data['date']) ?: new \DateTime());
        $event->setCreator($user);
        $event->setParticipants($this->getSpecificDataArray($data['participants'], $em->getRepository(User::class)));
        $event->setComments($this->getSpecificDataArray($data['comments'], $em->getRepository(Comment::class)));
        $event->setCategory($category);
        $event->setCity($city);
        $validator = $this->get('validator');
        $errors = $validator->validate($event);

        $this->checkIfParticipantIsNotCreator($event);

        if (count($errors) > 0) {
            throw new HttpException(400, $errors[0]->getMessage());
        }

        $em->persist($event);
        $em->flush();

        $view = View::create();
        $handler = $this->get('fos_rest.view_handler');
        $view->setData([
            'id' => $event->getId(),
            'name' => $event->getName(),
            'type' => $event->getType(),
            'description' => $event->getDescription(),
            'date' => $event->getDate(),
            'creator' => $event->getCreator()->getId(),
            'participants' => $this->getArrayData($event->getParticipants()),
            'comments' => $this->getArrayData($event->getComments()),
            'category' => $event->getCategory()->getId(),
            'city' => $event->getCity()->getId()
        ]);

        return $handler->handle($view);
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Gets list of events",
     *  output="ArrayCollection<AppBundle\Entity\Event>",
     *     statusCodes={
     *         200="Returned when successful"
     *     }
     * )
     *
     * @Rest\View
     * @throws \LogicException
     */
    public function allAction()
    {
        $events = $this->getDoctrine()
            ->getRepository(Event::class)
            ->findAll();

        $data = [];
        foreach ($events as $event) {
            $data[] = [
                'id' => $event->getId(),
                'name' => $event->getName(),
                'type' => $event->getType(),
                'description' => $event->getDescription(),
                'date' => $event->getDate(),
                'creator' => $event->getCreator()->getId(),
                'participants' => $this->getArrayData($event->getParticipants()),
                'comments' => $this->getArrayData($event->getComments()),
                'category' => $event->getCategory()->getId(),
                'city' => $event->getCity()->getId()
            ];
        }

        $view = View::create();
        $handler = $this->get('fos_rest.view_handler');
        $view->setData($data);

        return $handler->handle($view);
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Get an event",
     *  output="AppBundle\Entity\Event",
     *     statusCodes={
     *         200="Returned when successful",
     *          404="Returns when event not found"
     *     }
     * )
     *
     * @Rest\View
     * @throws NotFoundHttpException
     */
    public function getAction($id)
    {
        $event = $this->getDoctrine()
            ->getRepository(Event::class)
            ->find($id);

        if (!$event instanceof Event) {
            throw new NotFoundHttpException('Event not found');
        }

        $view = View::create();
        $handler = $this->get('fos_rest.view_handler');
        $view->setData([
            'id' => $event->getId(),
            'name' => $event->getName(),
            'type' => $event->getType(),
            'description' => $event->getDescription(),
            'date' => $event->getDate(),
            'creator' => $event->getCreator()->getId(),
            'participants' => $this->getArrayData($event->getParticipants()),
            'comments' => $this->getArrayData($event->getComments()),
            'category' => $event->getCategory()->getId(),
            'city' => $event->getCity()->getId()
        ]);

        return $handler->handle($view);
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Edit an event",
     *  output="AppBundle\Entity\Event",
     *     statusCodes={
     *         200="Returned when successful",
     *         400="Returned when the input parameters are bad",
     *         404="Returned when the event not found"
     *     }
     * )
     *
     * @Rest\View
     */
    public function editAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository(Event::class)
            ->find($id);

        if (!$event instanceof Event) {
            throw new NotFoundHttpException('Event not found');
        }

        $data = json_decode($request->getContent(), true);
        $event->setName($data['name'] ?: $event->getName());
        $event->setType($data['type'] ?: $event->getType());
        $event->setDescription($data['description'] ?: $event->getDescription());
        $event->setDate(isset($data['date']) ? new \DateTime($data['date']): $event->getDate());

        if (isset($data['creator'])) {
            $user = $em->getRepository(User::class)->find($data['creator']);
            if ($user) {
                $event->setCreator($user);
            }
        }

        $validator = $this->get('validator');
        $errors = $validator->validate($event);

        if (count($errors) > 0) {
            throw new HttpException(400, $errors[0]->getMessage());
        }

        $em->flush();

        $view = View::create();
        $handler = $this->get('fos_rest.view_handler');
        $view->setData([
            'id' => $event->getId(),
            'name' => $event->getName(),
            'type' => $event->getType(),
            'description' => $event->getDescription(),
            'date' => $event->getDate(),
            'creator' => $event->getCreator()->getId(),
            'participants' => $this->getArrayData($event->getParticipants()),
            'comments' => $this->getArrayData($event->getComments()),
            'category' => $event->getCategory()->getId(),
            'city' => $event->getCity()->getId()
        ]);

        return $handler->handle($view);
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Delete an event",
     *     statusCodes={
     *         204="Returned when successful",
     *         404="Returned when the event not found"
     *     }
     * )
     *
     * @Rest\View
     */
    public function deleteAction(Event $event)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($event);
        $em->flush();
    }

    public function participateAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $event = $this->getDoctrine()
            ->getRepository(Event::class)
            ->find($id);

        if (!$event instanceof Event) {
            throw new NotFoundHttpException('Event not found');
        }

        $data = json_decode($request->getContent(), true);
        if (isset($data['participant'])) {
            $user = $em->getRepository(User::class)->find($data['participant']);

            if (!$user) {
                throw new NotFoundHttpException('User not found');
            }

            $event->addParticipant($user);

            $this->checkIfParticipantIsNotCreator($event);
        }

        $em->flush();

        $view = View::create();
        $handler = $this->get('fos_rest.view_handler');
        $view->setData([
            'id' => $event->getId(),
            'name' => $event->getName(),
            'type' => $event->getType(),
            'description' => $event->getDescription(),
            'date' => $event->getDate(),
            'creator' => $event->getCreator()->getId(),
            'participants' => $this->getArrayData($event->getParticipants()),
            'comments' => $this->getArrayData($event->getComments()),
            'category' => $event->getCategory()->getId(),
            'city' => $event->getCity()->getId()
        ]);

        return $handler->handle($view);
    }

    /**
     * @param string|string[] $data
     * @param EntityRepository $repository
     * @return array
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws \InvalidArgumentException
     */
    private function getSpecificDataArray($data, EntityRepository $repository)
    {
        $items = [];
        if (isset($data)) {
            if (\is_array($data)) {
                foreach ($data as $datum) {
                    $object = $repository->find($datum);
                    if (!$object) {
                        throw new NotFoundHttpException(
                            $repository->getClassName().' with id '.$datum.' not found'
                        );
                    }

                    $items[] = $object;
                }
            } else {
                throw new HttpException(400, $repository->getClassName().' property must be an array');
            }
        }
        return $items;
    }

    /**
     * @param User[]|Comment[] $participants
     * @return array
     */
    private function getArrayData($participants)
    {
        $data = [];
        foreach ($participants as $participant) {
            $data[] = [
                'id' => $participant->getId(),
            ];
        }
        return $data;
    }

    /**
     * @param Event $event
     */
    private function checkIfParticipantIsNotCreator($event)
    {
        $creatorId = $event->getCreator()->getId();
        foreach ($event->getParticipants() as $participant) {
            if ($creatorId === $participant->getId()) {
                throw new HttpException(400, 'Creator is automatically a participant');
            }
        }
    }
}

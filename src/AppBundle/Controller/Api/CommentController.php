<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Comment;
use AppBundle\Entity\Event;
use FOS\RestBundle\View\View;
use SensioLabs\Security\Exception\HttpException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CommentController extends Controller
{
    /**
     * @Rest\View
     *
     * @param Request $request
     * @return Comment
     * @throws \InvalidArgumentException
     * @throws \Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws \SensioLabs\Security\Exception\HttpException
     * @throws \LogicException
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = json_decode($request->getContent(), true);

        $event = $em->getRepository(Event::class)->find($data['event']);
        if (!$event) {
            throw new NotFoundHttpException('Event not found');
        }

        $comment = new Comment();
        $comment->setContent($data['content'] ?: '');
        $comment->setPublishedAt(isset($data['publishedAt']) ? new \DateTime($data['publishedAt']) : new \DateTime());
        $comment->setEvent($event);
        $validator = $this->get('validator');
        $errors = $validator->validate($event);

        if (count($errors) > 0) {
            throw new HttpException(400, $errors[0]->getMessage());
        }

        $em->persist($comment);
        $em->flush();

        $view = View::create();
        $handler = $this->get('fos_rest.view_handler');
        $view->setData([
            'id' => $comment->getId(),
            'content' => $comment->getContent(),
            'published_at' => $comment->getPublishedAt(),
            'event' => $comment->getEvent()->getId(),
        ]);

        return $handler->handle($view);
    }

    /**
     * @Rest\View
     *
     * @return Comment[]
     * @throws \LogicException
     */
    public function allAction()
    {
        $comments = $this->getDoctrine()
            ->getRepository(Comment::class)
            ->findAll()
        ;

        $data = [];
        foreach ($comments as $comment) {
            $data[] = [
                'id' => $comment->getId(),
                'content' => $comment->getContent(),
                'published_at' => $comment->getPublishedAt(),
                'event' => $comment->getEvent()->getId(),
            ];
        }

        $view = View::create();
        $handler = $this->get('fos_rest.view_handler');
        $view->setData($data);

        return $handler->handle($view);
    }

    /**
     * @Rest\View
     *
     * @param $id
     * @return Comment|null
     * @throws \Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws \LogicException
     */
    public function getAction($id)
    {
        $comment = $this->getDoctrine()
            ->getRepository(Comment::class)
            ->find($id)
        ;

        if (!$comment instanceof Comment) {
            throw new NotFoundHttpException('Comment not found');
        }

        $view = View::create();
        $handler = $this->get('fos_rest.view_handler');
        $view->setData([
            'id' => $comment->getId(),
            'content' => $comment->getContent(),
            'published_at' => $comment->getPublishedAt(),
            'event' => $comment->getEvent()->getId(),
        ]);

        return $handler->handle($view);
    }

    /**
     * @Rest\View
     *
     * @param $id
     * @param Request $request
     * @return Comment|null
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws \LogicException
     */
    public function editAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $comment = $em->getRepository(Comment::class)
            ->find($id)
        ;

        if (!$comment instanceof Comment) {
            throw new NotFoundHttpException('Comment not found');
        }

        $data = json_decode($request->getContent(), true);
        $comment->setContent($data['content'] ?: $comment->getContent());
        $comment->setPublishedAt(
            isset($data['publishedAt']) ? new \DateTime($data['publishedAt']): $comment->getPublishedAt()
        );

        if (isset($data['event'])) {
            $event = $em->getRepository(Event::class)->find($data['event']);
            if ($event) {
                $comment->setEvent($event);
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
            'id' => $comment->getId(),
            'content' => $comment->getContent(),
            'published_at' => $comment->getPublishedAt(),
            'event' => $comment->getEvent()->getId(),
        ]);

        return $handler->handle($view);
    }

    /**
     * @Rest\View
     *
     * @param Comment $comment
     * @throws \LogicException
     */
    public function deleteAction(Comment $comment)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($comment);
        $em->flush();
    }
}

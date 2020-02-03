<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Entity\Responses;
use App\Form\ResponsesType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResponsesController extends AbstractController
{
    const PAGINATION_LIMIT = 10;

    /**
     * @Route("/{id}/view", name="responses.view", methods={"GET", "POST"})
     *
     * @param Comments $comment
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function view(Comments $comment, PaginatorInterface $paginator, Request $request): Response
    {
        $responses = new Responses();
        $params = [
            'method' => 'POST',
            'action' => $this->generateUrl(
                'responses.view',
                ['id' => $comment->getId()]
            ),
        ];
        $form = $this->createForm(ResponsesType::class, $responses, $params);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $responses->setCreated(new \DateTime('now'));
            $responses->setUpdated(new \DateTime('now'));
            $responses->setComment($comment);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($responses);
            $entityManager->flush();

            # Adding custom notifications
            $this->addFlash('success', 'Comment Created! ');

            return $this->redirectToRoute('responses.view', ['id' => $comment->getId()]);
        }

        $params = [
            'comment' => $comment,
            'paginator' => $paginator,
            'request' => $request,
            'form' => $form,
        ];

        return $this->returnIndex($params);
    }

    /**
     * @param array $params
     * @return Response
     */
    private function returnIndex(array $params): Response {

        $pagination = $params['paginator']->paginate(
            $params['comment']->getResponses(),
            $params['request']->query->getInt('page', 1),
            self::PAGINATION_LIMIT
        );

        return $this->render('responses/view.html.twig', [
            'comment' => $params['comment'],
            'pagination' => $pagination,
            'form' => $params['form']->createView(),
        ]);
    }
}

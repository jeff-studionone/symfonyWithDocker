<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Form\CommentsType;
use App\Repository\CommentsRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentsController extends AbstractController
{
    const PAGINATION_LIMIT = 10;

    /**
     * @Route("/", name="comments.index", methods={"GET","POST"})
     *
     * @param CommentsRepository $commentsRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function index(CommentsRepository $commentsRepository, PaginatorInterface $paginator, Request $request): Response
    {
        # Create an instance for a new comment
        $comment = new Comments();

        $form = $this->createForm(CommentsType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setCreated(new \DateTime('now'));
            $comment->setUpdated(new \DateTime('now'));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            # Adding custom notifications
            $this->addFlash('success', 'Comment Created! ');

            return $this->redirectToRoute('comments.index');
        }

        $params = [
            'repository' => $commentsRepository,
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
            $params['repository']->findAll(),
            $params['request']->query->getInt('page', 1),
            self::PAGINATION_LIMIT
        );

        return $this->render('comments/index.html.twig', [
            'pagination' => $pagination,
            'form' => $params['form']->createView(),
        ]);
    }
}

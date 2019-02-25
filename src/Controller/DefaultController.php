<?php

namespace App\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     *
     * @param Request $request
     * @param TaskRepository $taskRepository
     * @param PaginatorInterface $paginator
     *
     * @return Response
     */
    public function index(Request $request, TaskRepository $taskRepository, PaginatorInterface $paginator)
    {
        $list = $paginator->paginate(
            $taskRepository->findAllQueryBuilder(),
            $request->query->getInt('pg', 1),
            3
        );
        return $this->render('default/index.html.twig', [
            'list' => $list,
        ]);
    }

    /**
     * @Route("/add", name="task_add")
     * @Template("default/add.html.twig")
     *
     * @param Request $request
     * @param EntityManagerInterface $em
     *
     * @return array
     */
    public function add(Request $request, EntityManagerInterface $em)
    {
        $task = new Task();
        $form = $this->createFormBuilder($task)
            ->add('name')
            ->add('email')
            ->add('txt')
            ->add('save', SubmitType::class)
            ->getForm()
        ;
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task = $form->getData();
            $em->persist($task);
            $em->flush($task);

            return $this->redirectToRoute('homepage');
        }

        return [
            'form' => $form->createView(),
        ];
    }
}

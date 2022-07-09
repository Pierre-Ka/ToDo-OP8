<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

#[Security("is_granted('ROLE_USER')", message: 'Page Introuvable', statusCode: 404)]
// Depuis la mise en place du role hierarchie, je peux faire un ROLE_USER même pour un admin
//#[Security("is_granted(''IS_AUTHENTICATED_FULLY'')", message: 'Page Introuvable', statusCode: 404)]
class TaskController extends AbstractController
{
    /**
     * @Route("/tasks/list/undone", name="task_list_undone")
     */
    public function listTaskUndone(TaskRepository $taskRepository)
    {
        $tasks = $taskRepository->findBy(['isDone' => 0], ['createdAt' => 'DESC']);
        return $this->render('task/list.html.twig', ['tasks' => $tasks]);
    }

    /**
     * @Route("/tasks/list/done", name="task_list_done")
     */
    public function listTaskDone(TaskRepository $taskRepository)
    {
        $tasks = $taskRepository->findBy(['isDone' => 1], ['createdAt' => 'DESC']);
        return $this->render('task/list.html.twig', ['tasks' => $tasks]);
    }

    /**
     * @Route("/tasks/create", name="task_create")
     */
    public function createAction(Request $request, EntityManagerInterface $em)
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task->setUser($this->getUser());
            $em->persist($task);
            $em->flush();

            $this->addFlash('success', 'La tâche a été bien été ajoutée.');

            return $this->redirectToRoute('homepage');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/tasks/{id}/edit", name="task_edit")
     */
    public function editAction(Task $task, Request $request, EntityManagerInterface $em)
    {
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'La tâche a bien été modifiée.');

            return $this->redirectToRoute('homepage');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    /**
     * @Route("/tasks/{id}/toggle", name="task_toggle")
     */
    public function toggleTaskAction(Task $task, EntityManagerInterface $em)
    {
        $task->toggle(!$task->isDone());
        $em->flush();

        if($task->isDone())
        {
            $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme terminée', $task->getTitle()));
//            dd('Ici on teste légalité $task->isDone est true donc message terminée');
        }
        else
        {
            $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme non terminée.', $task->getTitle()));
//            dd('Ici on teste légalité $task->isDone est false donc message non terminée');
        }

        return $this->redirectToRoute('homepage');
    }

    /**
     * @Route("/tasks/{id}/delete", name="task_delete")
     */
    public function deleteTaskAction(Task $task, EntityManagerInterface $em)
    {
//        $this->denyAccessUnlessGranted('delete', $task); // voter ?

        $em->remove($task);
        $em->flush();

        $this->addFlash('success', 'La tâche a bien été supprimée.');

        return $this->redirectToRoute('homepage');
    }
}

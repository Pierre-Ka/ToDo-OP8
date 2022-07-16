<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Manager\TaskManagerInterface;
use App\Repository\TaskRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Security("is_granted('ROLE_USER')", message: 'Page Introuvable', statusCode: 404)]
class TaskController extends AbstractController
{
    #[Route('/tasks/undone', name: 'task_list_undone', methods: ['GET'])]
    public function listUndone(TaskRepository $taskRepository): Response {
        $tasks = $taskRepository->findBy(['isDone' => 0], ['createdAt' => 'DESC']);

        return $this->render('task/list.html.twig', ['tasks' => $tasks]);
    }

    #[Route('/tasks/done', name: 'task_list_done', methods: ['GET'])]
    public function listDone(TaskRepository $taskRepository): Response {
        $tasks = $taskRepository->findBy(['isDone' => 1], ['createdAt' => 'DESC']);

        return $this->render('task/list.html.twig', ['tasks' => $tasks]);
    }

    #[Route('/tasks/create', name: 'task_create', methods: ['GET', 'POST'])]
    public function create(Request $request, TaskManagerInterface $taskManager): Response {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $taskManager->new($task);
            $this->addFlash('success', 'La tâche a été bien été ajoutée.');

            return $this->redirectToRoute('homepage');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/tasks/{id}/edit', name: 'task_edit', methods: ['GET', 'POST'])]
    public function edit(Task $task, Request $request, TaskManagerInterface $taskManager): Response {
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $taskManager->update();
            $this->addFlash('success', 'La tâche a bien été modifiée.');

            return $this->redirectToRoute('homepage');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    #[Route('/tasks/{id}/toggle', name: 'task_toggle', methods: ['GET'])]
    public function toggle(Task $task, TaskManagerInterface $taskManager): Response {
        $taskManager->toggle($task);
        if($task->isDone())  {
            $this->addFlash('success', sprintf(
                'La tâche %s a bien été marquée comme terminée', $task->getTitle()));
        }
        else  {
            $this->addFlash('success', sprintf(
                'La tâche %s a bien été marquée comme non terminée.', $task->getTitle()));
        }

        return $this->redirectToRoute('homepage');
    }

    #[Route('/tasks/{id}/delete', name: 'task_delete', methods: ['GET', 'POST'])]
    public function delete(Task $task, TaskManagerInterface $taskManager): Response {
        $this->denyAccessUnlessGranted('delete', $task);
        $taskManager->delete($task);
        $this->addFlash('success', 'La tâche a bien été supprimée.');

        return $this->redirectToRoute('homepage');
    }
}

<?php

namespace App\Controller;

use App\Constants\ActivityActions;
use App\Entity\Activity;
use App\Entity\Comment;
use App\Entity\Task;
use App\Service\ActivityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    private $activityManager;

    public function __construct(ActivityManager $activityManager)
    {
        $this->activityManager = $activityManager;
    }

    /**
     * @Route("/", name="app_index")
     */
    public function index()
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $allTasks = $this->getDoctrine()
            ->getRepository(Task::class)
            ->findAllTasks();

        $tasks = $this->getDoctrine()
            ->getRepository(Task::class)
            ->findTasksByUserId($this->getUser()->getId());

        $countNotifications = $this->getDoctrine()
            ->getRepository(Activity::class)
            ->getCountUnreadActivitiesByUserId($this->getUser()->getId());

        $currentDate = new \DateTime();

        $previousTasks = array_filter($tasks, function($task) use ($currentDate) {
            return strtotime($task['created_at']->format('d-m-Y')) < strtotime($currentDate->format('d-m-Y'));
        });

        $todayTasks = array_filter($tasks, function($task) use ($currentDate) {
            return strtotime($task['created_at']->format('d-m-Y')) === strtotime($currentDate->format('d-m-Y'));
        });

        $toDoTasks = array_filter($tasks, function($task) use ($currentDate) {
            return strtotime($task['created_at']->format('d-m-Y')) > strtotime($currentDate->format('d-m-Y'));
        });

        return $this->render('base.html.twig', [
            'username' => $this->getUser()->getUsername(),
            'tasks' => $allTasks,
            'previousTasks' => $previousTasks,
            'todayTasks' => $todayTasks,
            'toDoTasks' => $toDoTasks,
            'countNotifications' => $countNotifications
        ]);
    }

    /**
     * @Route("/task/{id}", name="app_task_read")
     */
    public function read(Request $request, string $id)
    {
        $countNotifications = $this->getDoctrine()
            ->getRepository(Activity::class)
            ->getCountUnreadActivitiesByUserId($this->getUser()->getId());

        $task = $this->getDoctrine()
            ->getRepository(Task::class)
            ->findTasksByTaskId($id);

        if (!$task) {
            throw $this->createNotFoundException(
                'No task found for id ' . $id
            );
        }

        $changeStatusForm = $this->createFormBuilder()
            ->add('status', ChoiceType::class, [
                'placeholder' => 'Choose a status',
                'data' => $task['status'],
                'choices' => [
                    'OPEN' => 1,
                    'IN PROGRESS' => 2,
                    'DONE' => 3
                ]
            ])
            ->add('save', SubmitType::class, ['label' => 'Changes status'])
            ->getForm();

        $changeStatusForm->handleRequest($request);

        if ($changeStatusForm->isSubmitted() && $changeStatusForm->isValid()) {
            $formData = $changeStatusForm->getData();

            $this->getDoctrine()
                ->getRepository(Task::class)
                ->updateTaskStatus($task['id'], $formData['status']);

            $this->activityManager->logActivity(
                $this->getUser()->getId(),
                $task['id'],
                ActivityActions::UPDATE,
                'status',
                $task['status'],
                $formData['status'],
                ActivityActions::UNREAD_STATUS
            );

            return $this->redirectToRoute('app_task_read', ['id' => $task['id']]);
        }

        $comments = $this->getDoctrine()
            ->getRepository(Comment::class)
            ->findCommentByTaskIdUserId($id, $this->getUser()->getId());

        $commentForm = $this->createFormBuilder()
            ->add('comment', TextareaType::class)
            ->add('save', SubmitType::class, ['label' => 'Add comment'])
            ->getForm();

        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $formData = $commentForm->getData();

            $this->getDoctrine()
                ->getRepository(Comment::class)
                ->createComment($id, $this->getUser()->getId(), $formData['comment']);

            $this->activityManager->logActivity(
                $this->getUser()->getId(),
                $task['id'],
                ActivityActions::CREATE,
                'comment',
                '',
                $formData['comment'],
                ActivityActions::UNREAD_STATUS
            );

            return $this->redirectToRoute('app_task_read', ['id' => $task['id']]);
        }

        return $this->render(
            'task_layout.html.twig',
            [
                'username' => $this->getUser()->getUsername(),
                'task' => $task,
                'comments' => $comments,
                'commentForm' => $commentForm->createView(),
                'changeStatusForm' => $changeStatusForm->createView(),
                'activities' => $this->activityManager->getActivities($this->getUser()->getId(), $task['id']),
                'countNotifications' => $countNotifications
            ]
        );
    }

    /**
     * @Route("/task/delete/{id}", name="app_task_delete")
     */
    public function delete(string $id)
    {
        $task = $this->getDoctrine()
            ->getRepository(Task::class)
            ->find($id);

        if (!$task) {
            throw $this->createNotFoundException(
                'No task found for id ' . $id
            );
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($task);
        $em->flush();

        return $this->redirectToRoute('app_index');
    }
}
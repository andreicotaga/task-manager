<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Task;
use App\Entity\User;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserTaskRepository")
 */
class UserTask
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     *  @var Task
     *
     * @ORM\Column(type="integer")
     * @ORM\ManyToOne(targetEntity="Task")
     * @ORM\JoinColumn(name="task_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $task_id;

    /**
     *  @var User
     *
     * @ORM\Column(type="integer")
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $user_id;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTaskId(): ?int
    {
        return $this->task_id;
    }

    public function setTaskId(int $task_id): self
    {
        $this->task_id = $task_id;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setUserId(int $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }
}

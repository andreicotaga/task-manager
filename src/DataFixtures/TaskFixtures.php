<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\UserTask;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class TaskFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(\Doctrine\Common\Persistence\ObjectManager $manager)
    {
        for ($i = 0; $i < 50; $i++)
        {
            $task = new Task();

            $task->setTitle('Random title ' . $i);
            $task->setCreatedBy($this->getReference(UserFixtures::ADMIN_USER_REFERENCE)->getId());
            $task->setDescription('Random description ' . $i);
            $task->setStatus(1);
            $task->setCreatedAt(new \DateTime());
            $task->setUpdatedAt(new \DateTime());

            $manager->persist($task);
            $manager->flush();

            $userTask = new UserTask();

            $userTask->setTaskId(rand(1, 50));
            $userTask->setUserId(rand(2, 21));

            $manager->persist($userTask);
            $manager->flush();
        }
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class
        ];
    }
}

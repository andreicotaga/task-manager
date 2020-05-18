<?php

namespace App\DataFixtures;

use App\Entity\TaskStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TaskStatusFixtures extends Fixture
{
    const STATUS = ['OPEN', 'IN PROGRESS', 'DONE'];

    public function load(\Doctrine\Common\Persistence\ObjectManager $manager)
    {
        foreach(self::STATUS as $value)
        {
            $task = new TaskStatus();

            $task->setName($value);

            $manager->persist($task);
            $manager->flush();
        }
    }
}

<?php
/**
 * Task fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Task;
use Doctrine\Persistence\ObjectManager;

/**
 * Class TaskFixtures.
 */
class TaskFixtures extends AbstractBaseFixtures
{
    /**
     * Load data.
     *
     * @param \Doctrine\Persistence\ObjectManager $manager Persistence object manager
     */
    public function loadData(ObjectManager $manager): void
    {
        for ($i = 0; $i < 10; ++$i) {
            $task = new Task();
            $task->setTitle($this->faker->sentence);
            $task->setCreatedAt($this->faker->dateTimeBetween('-100 days', '-1 days'));
            $task->setUpdatedAt($this->faker->dateTimeBetween('-100 days', '-1 days'));
            $this->manager->persist($task);
        }

        $manager->flush();
    }
}
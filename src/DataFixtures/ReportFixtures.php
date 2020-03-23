<?php

namespace App\DataFixtures;

use App\Entity\Message;
use App\Entity\Report;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class ReportFixtures extends BaseFixtures implements DependentFixtureInterface
{
    public function loadData(ObjectManager $manager): void
    {
        $this->createMany(Report::class, 30, function (Report $report) {
            /** @var Message $reportedMessage */
            $reportedMessage = $this->getRandomReference(Message::class);

            $report->setMessage($reportedMessage)
                ->setReason($this->faker->sentence)
                ->setReportedAt($this->faker->dateTimeBetween($reportedMessage->getPublishedAt()))
                ->setReportedBy($this->getRandomReference(User::class));

            if ($this->faker->boolean(65)) {
                $report->setTreatedAt($this->faker->dateTimeBetween($report->getReportedAt()))
                    ->setTreatedBy($this->getRandomReference(User::class));
            }
        });

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            MessageFixtures::class
        ];
    }
}

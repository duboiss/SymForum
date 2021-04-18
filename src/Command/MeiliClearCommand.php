<?php

namespace App\Command;

use App\Service\MeiliSearchService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MeiliClearCommand extends Command
{
    protected static $defaultName = 'app:meili:clear';
    protected static string $defaultDescription = 'Delete all indexes in MeiliSearch';

    public function __construct(private MeiliSearchService $meiliSearchService, string $name = null)
    {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->setDescription(self::$defaultDescription)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->meiliSearchService->deleteAllIndexes();
        $io->success('All indexes in MeiliSearch have been deleted.');

        return Command::SUCCESS;
    }
}

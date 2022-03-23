<?php

namespace App\Command;

namespace App\Command;

use App\Service\Book\ExportInCsv;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExportBooksInCsvCommand extends Command
{
    protected static $defaultName = 'app:books:export-in-csv';
    
    public function __construct(private ExportInCsv $exportInCsv)
    {
        parent::__construct();
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        ($this->exportInCsv)();
        $output->writeln('CSV wrote successfully');
        return Command::SUCCESS;
    }
}
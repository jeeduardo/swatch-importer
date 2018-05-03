<?php

namespace Josephson\SwatchImporter\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Psr\Log\LoggerInterface;

class ImportSwatchesCommand extends Command
{
    /**
     * @var \Josephson\SwatchImporter\Model\Import\SwatchCsvProcessor
     */
    protected $csvProcessor;

    public function __construct(
        \Josephson\SwatchImporter\Model\Import\SwatchCsvProcessor $csvProcessor,
        \Psr\Log\LoggerInterface $logger
    ) {
        // $csvProcessor = \Magento\Framework\App\ObjectManager::get('Josephson\SwatchImporter\Model\Import\SwatchCsvProcessor');
        $this->csvProcessor = $csvProcessor;
        $this->logger = $logger;
        parent::__construct();
    }

    protected function configure()
    {
        $definition = [
            new InputOption(
                'file',
                '-f',
                InputOption::VALUE_REQUIRED,
                'CSV import file to use'
            )
        ];
        $this->setName('josephson:swatchimporter:import')
            ->setDescription('Import Swatch Options from CSV file')
            ->setDefinition($definition);
    }

    /**
     * Console command execution
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $input->getOption('file');
        $basePath = BP;
        $wholePathToFile = $basePath . '/' . $file;

        $output->writeln('Checking file availability...');
        if (is_readable($wholePathToFile)) {
            // read contents from file
            // parse from CSV to some array
            // iterate
            $this->csvProcessor->processCsvFile($wholePathToFile);
            return;
        }
        $output->writeln('File doesn\'t exist... :(');
    }
}

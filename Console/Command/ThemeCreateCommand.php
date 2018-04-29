<?php

namespace Restoreddev\CliEnhanced\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ThemeCreateCommand extends Command
{
    protected function configure()
    {
        $this->setName('theme:create')
             ->setDescription('Generates theme based on Magento Blank');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('');
    }
}
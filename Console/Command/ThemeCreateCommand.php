<?php

namespace Restoreddev\CliEnhanced\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Question\Question;
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
        $DS = DIRECTORY_SEPARATOR;
        $helper = $this->getHelper('question');
        $nameQuestion = new Question('Please enter the namespace and name of the theme like "CompanyName/luma": ');

        $answer = $helper->ask($input, $output, $nameQuestion);

        $parts = explode('/', $answer);
        if (count($parts) != 2) {
            $output->writeln('The theme name is invalid.');
            return;
        }

        $namespace = $parts[0];
        $name = $parts[1];

        $cwd = getcwd();
        $path = $cwd . $DS . 'app' . $DS . 'design' . $DS . 'frontend' . $DS . $namespace . $DS . $name;
        $themeXml = file_get_contents(__DIR__ . $DS . '..' . $DS . '..' . $DS . 'templates' . $DS . 'theme.xml');
        $themeXml = str_replace('THEME_NAME', $namespace . ' ' . $name, $themeXml);
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        file_put_contents($path . $DS . 'theme.xml', $themeXml);

        $output->writeln("\"$namespace $name\" theme has been created in app/frontend");
    }
}
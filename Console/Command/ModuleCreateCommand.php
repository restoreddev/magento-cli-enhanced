<?php

namespace Restoreddev\CliEnhanced\Console\Command;

use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class ModuleCreateCommand extends BaseCommand
{
    /**
     * Namespace for the module
     * 
     * @var string
     */
    protected $moduleNamespace;

    /**
     * Name of module being created
     * 
     * @var string
     */
    protected $moduleName;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('module:create')
             ->setDescription('Generates module')
             ->setHelp(
                 "The command asks for the module namespace and name and creates it in app/code"
             );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!file_exists(getcwd() . self::DS . 'app')) {
            $output->writeln('Please run in the project root.');
            return;
        }

        $helper = $this->getHelper('question');
        $nameQuestion = new Question('Please enter the namespace and name of the module e.g. "CompanyName/Sales": ');

        $answer = $helper->ask($input, $output, $nameQuestion);

        $parts = explode('/', $answer);
        if (count($parts) != 2) {
            $output->writeln('The module name is invalid.');
            return;
        }

        $this->moduleNamespace = $parts[0];
        $this->moduleName = $parts[1];

        if ($this->checkModuleExists()) {
            $confirm = new ConfirmationQuestion('A module with this name already exists, continue (y/n)? ', false);
            $result = $helper->ask($input, $output, $confirm);

            if (!$result) {
                return;
            }
        }

        $moduleXml = $this->getTemplateContents('module', 'module.xml');
        $moduleXml = str_replace('MODULE_NAME', $this->moduleNamespace . '_' . $this->moduleName, $moduleXml);

        $moduleRegister = $this->getTemplateContents('module', 'registration.php');
        $moduleRegister = str_replace(
            'MODULE_NAME',
            $this->moduleNamespace . '_' . $this->moduleName,
            $moduleRegister
        );

        $moduleComposer = $this->getTemplateContents('module', 'composer.json');
        $moduleComposer = str_replace(
            ['MODULE_NAME_ESCAPED', 'MODULE_NAME'],
            [
                $this->moduleNamespace . '\\\\' . $this->moduleName . '\\\\',
                strtolower($this->moduleNamespace . '/' . $this->moduleName),
            ],
            $moduleComposer
        );

        $this->createModuleFolders();
        $this->putFileInModule('etc' . self::DS . 'module.xml', $moduleXml);
        $this->putFileInModule('registration.php', $moduleRegister);
        $this->putFileInModule('composer.json', $moduleComposer);

        $output->writeln("\"$this->moduleNamespace $this->moduleName\" theme has been created in app/frontend");
    }

    /**
     * Returns path to module folder
     * 
     * @return string
     */
    protected function getModulePath()
    {
        return getcwd() . self::DS .
               'app' . self::DS .
               'code' . self::DS .
               $this->moduleNamespace . self::DS .
               $this->moduleName;
    }

    /**
     * Adds file to module folder
     * 
     * @param  string $filename
     * @param  string $contents
     * @return void
     */
    protected function putFileInModule($filename, $contents)
    {
        $path = $this->getModulePath();

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        file_put_contents($path . self::DS . $filename, $contents);
    }

    /**
     * Checks if theme exists
     * 
     * @return bool
     */
    protected function checkModuleExists()
    {
        if (file_exists($this->getModulePath() . self::DS . 'etc' . self::DS . 'module.xml')) {
            return true;
        }

        return false;
    }

    /**
     * Creates all the static file folders in theme
     * 
     * @return void
     */
    protected function createModuleFolders()
    {
        $path = $this->getModulePath();

        $paths = [
            $path . self::DS . 'etc',
        ];

        foreach ($paths as $path) {
            if (file_exists($path)) {
                continue;
            }

            mkdir($path, 0777, true);
        }
    }
}

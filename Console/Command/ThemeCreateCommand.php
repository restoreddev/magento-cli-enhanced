<?php

namespace Restoreddev\CliEnhanced\Console\Command;

use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class ThemeCreateCommand extends BaseCommand
{
    /**
     * Namespace for the theme
     * 
     * @var string
     */
    protected $themeNamespace;

    /**
     * Name of theme being created
     * 
     * @var string
     */
    protected $themeName;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('theme:create')
             ->setDescription('Generates frontend theme')
             ->setHelp(
                "Creates a new frontend theme.\n" .
                "The command asks for the theme name and then generates a custom theme based on Magento/blank and\n" .
                'generates all the static assets folders.'
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
        $nameQuestion = new Question('Please enter the namespace and name of the theme e.g. "CompanyName/luma": ');

        $answer = $helper->ask($input, $output, $nameQuestion);

        $parts = explode('/', $answer);
        if (count($parts) != 2) {
            $output->writeln('The theme name is invalid.');
            return;
        }

        $this->themeNamespace = $parts[0];
        $this->themeName = $parts[1];

        if ($this->checkThemeExists()) {
            $confirm = new ConfirmationQuestion('A theme with this name already exists, continue (y/n)? ', false);
            $result = $helper->ask($input, $output, $confirm);

            if (!$result) {
                return;
            }
        }

        $themeXml = $this->getTemplateContents('theme', 'theme.xml');
        $themeXml = str_replace('THEME_NAME', $this->themeNamespace . ' ' . $this->themeName, $themeXml);

        $themeRegister = $this->getTemplateContents('theme', 'registration.php');
        $themeRegister = str_replace(
            'COMPONENT_NAME',
            'frontend/' . $this->themeNamespace . '/' . $this->themeName,
            $themeRegister
        );

        $themeComposer = $this->getTemplateContents('theme', 'composer.json');
        $themeComposer = str_replace(
            'MODULE_NAME',
            strtolower($this->themeNamespace . '/' . $this->themeName),
            $themeComposer
        );

        $themeExtends = $this->getTemplateContents('theme', '_extend.less');

        $this->putFileInTheme('theme.xml', $themeXml);
        $this->putFileInTheme('registration.php', $themeRegister);
        $this->putFileInTheme('composer.json', $themeComposer);

        $this->createThemeStaticFolders();

        $this->putFileInTheme(
            'web' . self::DS .
            'css' . self::DS . 'source' .
            self::DS . '_extend.less',
            $themeExtends
        );

        $output->writeln("\"$this->themeNamespace $this->themeName\" theme has been created in app/frontend");
    }

    /**
     * Adds file to theme folder
     * 
     * @param  string $filename
     * @param  string $contents
     * @return void
     */
    protected function putFileInTheme($filename, $contents)
    {
        $themePath = $this->getThemePath();

        if (!file_exists($themePath)) {
            mkdir($themePath, 0777, true);
        }

        file_put_contents($themePath . self::DS . $filename, $contents);
    }

    /**
     * Returns path to theme folder
     * 
     * @return string
     */
    protected function getThemePath()
    {

        return getcwd() . self::DS .
               'app' . self::DS .
               'design' . self::DS .
               'frontend' . self::DS .
               $this->themeNamespace . self::DS .
               $this->themeName;
    }

    /**
     * Creates all the static file folders in theme
     * 
     * @return void
     */
    protected function createThemeStaticFolders()
    {
        $themePath = $this->getThemePath();
        $themePath .= self::DS . 'web';

        $paths = [
            $themePath . self::DS . 'css' . self::DS . 'source',
            $themePath . self::DS . 'js',
            $themePath . self::DS . 'images',
            $themePath . self::DS . 'fonts',
        ];

        foreach ($paths as $path) {
            if (file_exists($path)) {
                continue;
            }

            mkdir($path, 0777, true);
        }
    }

    /**
     * Checks if theme exists
     * 
     * @return bool
     */
    protected function checkThemeExists()
    {
        if (file_exists($this->getThemePath() . self::DS . 'theme.xml')) {
            return true;
        }

        return false;
    }
}

<?php

namespace RestoredDev\CliEnhanced\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BaseCommand extends Command
{
    /**
     * Directory separator constant
     */
    const DS = DIRECTORY_SEPARATOR;

    /**
     * Returns contents of template file
     * 
     * @param  string $filename
     * @return string
     */
    protected function getTemplateContents($folder, $filename)
    {
        return file_get_contents(
            __DIR__ . self::DS .
            '..' . self::DS .
            '..' . self::DS .
            'templates' . self::DS .
            $folder . self::DS .
            $filename
        );
    }
}

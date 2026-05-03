<?php

namespace App\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateControllerCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('make:controller')
             ->setDescription('Creates a new controller file')
             ->addArgument('name', InputArgument::REQUIRED, 'The name of the controller class');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');
        $path = __DIR__ . '/../Controllers/' . $name . '.php';

        if (file_exists($path)) {
            $output->writeln("<error>Controller already exists!</error>");
            return Command::FAILURE;
        }

        $stub = file_get_contents(__DIR__ . '/../Stubs/Controller.stub');
        $content = str_replace('{{name}}', $name, $stub);

        file_put_contents($path, $content);

        $output->writeln("<info>Controller created successfully at $path</info>");
        return Command::SUCCESS;
    }
}

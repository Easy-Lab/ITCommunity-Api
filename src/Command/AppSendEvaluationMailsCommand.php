<?php

namespace App\Command;

use App\Service\EvaluationService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AppSendEvaluationMailsCommand extends Command
{
    protected static $defaultName = 'app:send-evaluation-mails';
    protected $container;

    /**
     * AppCheckCronCommand constructor.
     * @param $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct();
        $this->container = $container;
    }

    protected function configure()
    {
        $this->setDescription('Envoie un mail 48h après une communication');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $io->comment('Sending evaluation mails...');

        $evaluationService = $this->container->get(EvaluationService::class);

        if($evaluationService->commandEvaluation()) {
            $io->success('Evaluation mails sent !');
        }
        else {
            $io->error('Failed to send evaluation mails.');
        }
    }
}

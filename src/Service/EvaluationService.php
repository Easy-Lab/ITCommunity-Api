<?php


namespace App\Service;


use App\Service\Manager\EvaluationManager;
use App\Service\Manager\MessageManager;
use App\Utils\Mailer;

class EvaluationService
{
    public $messageManager;
    public $evaluationManager;
    public $mailer;

    public function __construct(MessageManager $messageManager, EvaluationManager $evaluationManager, Mailer $mailer)
    {
        $this->messageManager=$messageManager;
        $this->evaluationManager=$evaluationManager;
        $this->mailer=$mailer;
    }

    public function commandEvaluation()
    {
        $messages = $this->messageManager->findMessages();
        $past = new \DateTime();
        $past->setTimestamp(strtotime('-2 days'));
        $past_string = $past->format('Y-m-d');
        foreach ($messages as $message){
            $evaluation = $this->evaluationManager->findEvaluationExist($message->getUser(),$message->getContact());
            if (!$evaluation)
            {
                if ($message->getCreatedAt()->format('Y-m-d') == $past_string )
                {
                    $this->mailer->sendEvaluationMail($message->getUser(), $message->getContact(),$message->getHash());
                }
            }
        }
        return true;
    }
}

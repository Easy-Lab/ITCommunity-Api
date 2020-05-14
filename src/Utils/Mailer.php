<?php


namespace App\Utils;


use App\Entity\Affiliate;
use App\Entity\BugReport;
use App\Entity\Contact;
use App\Entity\ContactForm;
use App\Entity\Evaluation;
use App\Entity\Message;
use App\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class Mailer
{
    private $translator;
    private $templating;
    private $container;
    private $logger;

    public function __construct(TranslatorInterface $translator, Environment $templating, ContainerInterface $container, LoggerInterface $logger)
    {
     $this->translator = $translator;
     $this->container = $container;
     $this->templating = $templating;
     $this->logger = $logger;
    }

    public function sendEvaluationMail(User $user, Contact $contact, string $hash)
    {
            return $this->send('contact.evaluate', $contact['email'], [
                'user' => $user,
                'contact' => $contact,
                'urlEvaluation'=>'https://itcommunity.fr/evaluation/'.$hash,
                'login'=>'https://itcommunity.fr/se-connecter'
            ]);
    }

    public function sendInvitationMail(Affiliate $affiliate)
    {
        return $this->send('contact.invitation', $affiliate->getEmail(), [
            'user' => $affiliate->getUser(),
            'firstname' => $affiliate->getFirstname(),
            'lastname' => $affiliate->getLastname(),
            'hash'=>$affiliate->getHash(),
            'urlFront'=>getenv('URL_FRONT').'/discover',
            'login'=>getenv('URL_FRONT').'/se-connecter'
        ]);
    }

    public function sendActivationMail(User $user)
    {
        return $this->send('user.activation', $user->getEmail(), [
            'user' => $user,
            'hash'=>$user->getHash(),
            'login'=>'https://itcommunity.fr/se-connecter'
        ]);
    }

    public function sendPrivateMessageMail(Message $message, User $user)
    {
        return $this->send('user.message_private', $user->getEmail(), [
            'user' => $user,
            'message' => $message,
            'login'=>'https://itcommunity.fr/se-connecter'
        ], [$message->getContact()->getEmail()]);
    }

    public function sendAnswerMessageMail(Message $message, Contact $contact)
    {
        return $this->send('contact.message', $contact->getEmail(), [
            'contact' => $contact,
            'message' => $message,
            'login'=>'https://itcommunity.fr/se-connecter'
        ], [$message->getUser()->getEmail()]);
    }

    public function sendPublicMessageMail(Message $message, User $user)
    {
       return $this->send('user.message_public', $user->getEmail(), [
            'user' => $user,
            'message'=>$message,
            'urlFront'=>'https://itcommunity.fr/user/message',
            'login'=>'https://itcommunity.fr/se-connecter'
       ], [$message->getContact()->getEmail()]);
    }

    public function sendNewEvaluationMail(Evaluation $evaluation, User $user)
    {
        return $this->send('user.evaluation', $user->getEmail(), [
            'user' => $user,
            'evaluation' => $evaluation,
            'urlFront'=>'https://itcommunity.fr/user/evaluation',
            'login'=>'https://itcommunity.fr/se-connecter'
        ], [$evaluation->getContact()->getEmail()]);
    }

    public function sendContactFormMail(ContactForm $contactForm)
    {
        return $this->send('admin.contact_us', getenv('MAILER_FROM_CONTACT'), [
            'contactForm' => $contactForm,
            'login'=>'https://itcommunity.fr/se-connecter'
        ]);
    }

    public function sendReportFormMail(BugReport $bugReport)
    {
        return $this->send('admin.bug_report', getenv('MAILER_FROM_CONTACT'), [
            'bugReport' => $bugReport,
            'login'=>'https://itcommunity.fr/se-connecter'
        ]);
    }

    public function sendRequestPasswordMail(User $user)
    {
        return $this->send('account.forgot_password', $user->getEmail(), [
            'user' => $user,
            'urlFront'=>'https://itcommunity.fr/user/forgot-password/'.$user->getHash(),
            'login'=>'https://itcommunity.fr/se-connecter'
        ]);
    }

    public function send($slug, $to, array $vars, $replyTo = [], $object=null, $mailAdmin=null)
    {
        $success = false;
        $error = null;
        try {
            if(!is_array($to)) $to = [$to];
            if (!is_array($replyTo)) $replyTo = [$replyTo];
            // $to est désormais forcément un tableau d'adresses mail
            //$to = ['contact@lesambassadeurstryba.fr']; // TODO : delete

            // Objet du mail
            /** @Ignore */

            if (is_null($object)) {
                $subject = $this->translator->trans("mail.$slug.subject");
            }else{
                $subject = $object;
            }
            $mailFrom = null;
            if ($mailAdmin){
                $mailFrom = getenv('MAILER_FROM_CONTACT');
            }else{
                $mailFrom = getenv('MAILER_FROM_ADDRESS');
            }
            // Chemin du template du mail
            $template = "mails/".str_replace('.', '/', $slug).".html.twig";
            $body = $this->templating->render($template, $vars);
            $message = (new \Swift_Message())
                ->setSubject($subject)
                ->setFrom([
                    $mailFrom => getenv('MAILER_FROM_LABEL')
                ])
                ->setTo($to[0])
                ->setReplyTo($replyTo)
                ->setBody($body ,'text/html');
            $this->container->get('mailer')->send($message);
            $success = true;
        }
        catch(\Throwable $e) {
            $error = $e->getMessage();
            $this->logger->error("MailService: $error");
        }

        return $success;
    }

}

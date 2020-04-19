<?php


namespace App\Utils;


use App\Entity\BugReport;
use App\Entity\Contact;
use App\Entity\ContactForm;
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
                'urlEvaluation'=>getenv('URL_FRONT').'/evaluation/'.$hash,
                'login'=>getenv('URL_FRONT').'/se-connecter'
            ]);
    }

    public function sendInvitationMail(Message $message)
    {
        return $this->send('contact.invitation', $message->getContact()->getEmail(), [
            'user' => $message->getUser(),
            'contact' => $message->getContact(),
            'hash'=>$message->getHash(),
            'urlFront'=>getenv('URL_FRONT').'/user/log',
            'login'=>getenv('URL_FRONT').'/se-connecter'
        ]);
    }

    public function sendActivationMail(User $user)
    {
        return $this->send('user.activation', $user->getEmail(), [
            'user' => $user,
            'hash'=>$user->getHash(),
            'login'=>getenv('URL_FRONT').'/se-connecter'
        ]);
    }

    public function sendPrivateMessageMail(Message $message, User $user)
    {
        return $this->send('user.message_private', $user->getEmail(), [
            'user' => $user,
            'message' => $message,
            'login'=>getenv('URL_FRONT').'/se-connecter'
        ], [$message->getContact()->getEmail()]);
    }

    public function sendPublicMessageMail(Message $message, User $user)
    {
       return $this->send('user.message_public', $user->getEmail(), [
            'user' => $user,
            'message'=>$message,
            'urlFront'=>getenv('URL_FRONT').'/user/message',
            'login'=>getenv('URL_FRONT').'/se-connecter'
       ], [$message->getContact()->getEmail()]);
    }

    public function sendContactFormMail(ContactForm $contactForm)
    {
        return $this->send('admin.contact_us', getenv('MAILER_FROM_CONTACT'), [
            'contactForm' => $contactForm,
            'login'=>getenv('URL_FRONT').'/se-connecter'
        ]);
    }

    public function sendReportFormMail(BugReport $bugReport)
    {
        return $this->send('admin.bug_report', getenv('MAILER_FROM_CONTACT'), [
            'bugReport' => $bugReport,
            'login'=>getenv('URL_FRONT').'/se-connecter'
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
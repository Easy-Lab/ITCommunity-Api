<?php


declare(strict_types=1);

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Point;
use App\Exception\ApiException;
use App\Form\Filter\MessageFilter;
use App\Form\MessageAnswerType;
use App\Form\MessageType;
use App\Interfaces\ControllerInterface;
use App\Service\Manager\UserManager;
use App\Service\Manager\ContactManager;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


/**
 * @Route(path="/messages")
 */
class MessageController extends AbstractController implements ControllerInterface
{
    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * @var ContactManager
     */
    private $contactManager;

    /**
     * MessageController constructor.
     * @param UserManager $userManager
     * @param ContactManager $contactManager
     */
    public function __construct(UserManager $userManager, ContactManager $contactManager)
    {
        parent::__construct(Message::class);
        $this->userManager = $userManager;
        $this->contactManager = $contactManager;
    }

    /**
     * Get all Messages.
     *
     * @Route(name="api_message_list", methods={Request::METHOD_GET})
     *
     * @SWG\Tag(name="Message")
     * @SWG\Response(
     *     response=200,
     *     description="Returns the list of message",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Message::class))
     *     )
     * )
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function listAction(Request $request): JsonResponse
    {
        return $this->createCollectionResponse(
            $this->handleFilterForm(
                $request,
                MessageFilter::class
            )
        );
    }

    /**
     * Add new Message.
     *
     * @Route(name="api_message_create", methods={Request::METHOD_POST})
     *
     * @SWG\Tag(name="Message")
     * @SWG\Response(
     *     response=200,
     *     description="Updates Message of given identifier and returns the updated object.",
     *     @SWG\Schema(
     *         type="object",
     *         @SWG\Items(ref=@Model(type=Message::class))
     *     )
     * )
     *
     * @param Request $request
     * @param Message|null $message
     * @return JsonResponse
     */
    public function createAction(Request $request, Message $message = null): JsonResponse
    {
        $data = \json_decode($request->getContent(), true);
        $contact = $this->contactManager->findContactByEmail($data['email']);
        $user = $this->userManager->findUserByUsername($data['username']);

        if(!$contact or !$user) {
            return $this->createNotFoundResponse();
        }

        if (!$message) {
            $message = new Message();
            $message->setContact($contact);
            $message->setUser($user);
        }

        $form = $this->getForm(
            MessageType::class,
            $message,
            [
                'method' => $request->getMethod(),
            ]
        );

        try {
            $this->formHandler->process($request, $form);
            $point = new Point();
            $point->setMessage($message);
            $point->setUser($user);
            $point->setAmount(25);
            $point->setType('Question');
            $this->entityManager->persist($point);
            $this->entityManager->flush();
        } catch (ApiException $e) {
            return new JsonResponse($e->getData(), Response::HTTP_BAD_REQUEST);
        }

        return $this->createResourceResponse($message, Response::HTTP_CREATED);
    }


    /**
     * Edit existing Message.
     *
     * @Route(path="/{message}", name="api_message_edit", methods={Request::METHOD_PATCH})
     *
     * @SWG\Tag(name="Message")
     * @SWG\Response(
     *     response=200,
     *     description="Updates Message of given identifier and returns the updated object.",
     *     @SWG\Schema(
     *         type="object",
     *         @SWG\Items(ref=@Model(type=Message::class))
     *     )
     * )
     *
     * @param Request $request
     * @param Message|null $message
     * @return JsonResponse
     *
     * @Security("is_granted('CAN_UPDATE_USER', user)")
     */
    public function updateAction(Request $request, Message $message = null): JsonResponse
    {
        if (!$message) {
            return $this->createNotFoundResponse();
        }

        $form = $this->getForm(
            MessageType::class,
            $message,
            [
                'method' => $request->getMethod(),
            ]
        );

        try {
            $this->formHandler->process($request, $form);
        } catch (ApiException $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        return $this->createResourceResponse($message);
    }

    /**
     * Message Answer.
     *
     * @Route(path="/answer/{message}", name="api_message_answer_edit", methods={Request::METHOD_PATCH})
     *
     * @SWG\Tag(name="Message")
     * @SWG\Response(
     *     response=200,
     *     description="Updates Answer Message of given identifier and returns the updated object.",
     *     @SWG\Schema(
     *         type="object",
     *         @SWG\Items(ref=@Model(type=Message::class))
     *     )
     * )
     *
     * @param Request $request
     * @param Message|null $message
     * @return JsonResponse
     *
     * @Security("is_granted('CAN_UPDATE_USER', message)")
     */
    public function updateAnswerAction(Request $request, Message $message = null): JsonResponse
    {
        if (!$message) {
            return $this->createNotFoundResponse();
        }

        $form = $this->getForm(
            MessageAnswerType::class,
            $message,
            [
                'method' => $request->getMethod(),
            ]
        );

        try {
            $this->formHandler->process($request, $form);
        } catch (ApiException $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        return $this->createResourceResponse($message);
    }


    /**
     * Delete Message.
     *
     * @Route(path="/{message}", name="api_message_delete", methods={Request::METHOD_DELETE})
     *
     * @SWG\Tag(name="Message")
     * @SWG\Response(
     *     response=200,
     *     description="Delete message of given identifier and returns the empty object.",
     *     @SWG\Schema(
     *         type="object",
     *         @SWG\Items(ref=@Model(type=Message::class))
     *     )
     * )
     *
     * @param Message|null $message
     * @return JsonResponse
     *
     * @Security("is_granted('CAN_DELETE_USER', user)")
     */
    public function deleteAction(Message $message = null): JsonResponse
    {
        if (!$message) {
            return $this->createNotFoundResponse();
        }

        try {
            $message->setUser(null);
            $message->setContact(null);
            $this->entityManager->remove($message);
            $this->entityManager->flush();
        } catch (\Exception $exception) {
            return $this->createGenericErrorResponse($exception);
        }

        return $this->createSuccessfulApiResponse(self::DELETED);
    }
}
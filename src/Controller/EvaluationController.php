<?php


namespace App\Controller;

use App\Entity\Evaluation;
use App\Entity\Point;
use App\Exception\ApiException;
use App\Form\EvaluationType;
use App\Form\Filter\EvaluationFilter;
use App\Interfaces\ControllerInterface;
use App\Service\Manager\ContactManager;
use App\Service\Manager\EvaluationManager;
use App\Service\Manager\MessageManager;
use App\Service\Manager\UserManager;
use App\Utils\Mailer;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route(path="/evaluations")
 */
class EvaluationController extends AbstractController implements ControllerInterface
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
     * @var EvaluationManager
     */
    private $evaluationManager;

    /**
     * @var MessageManager
     */
    private $messageManager;

    /**
     * @var Mailer
     */
    private $mailer;


    /**
     * ContactController constructor.
     * @param UserManager $userManager
     * @param ContactManager $contactManager
     * @param MessageManager $messageManager
     * @param EvaluationManager $evaluationManager
     * @param Mailer $mailer
     */
    public function __construct(UserManager $userManager, ContactManager $contactManager, MessageManager $messageManager, EvaluationManager $evaluationManager, Mailer $mailer)
    {
        parent::__construct(Evaluation::class);
        $this->userManager = $userManager;
        $this->contactManager = $contactManager;
        $this->messageManager = $messageManager;
        $this->evaluationManager = $evaluationManager;
        $this->mailer = $mailer;
    }

    /**
     * Get all Evaluation.
     *
     * @Route(name="api_evaluation_list", methods={Request::METHOD_GET})
     *
     * @SWG\Tag(name="Evaluation")
     * @SWG\Response(
     *     response=200,
     *     description="Returns the list of evaluation",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Evaluation::class))
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
                EvaluationFilter::class
            )
        );
    }

    /**
     * Show single Evaluation.
     *
     * @Route(path="/{hash}", name="api_single_evaluation_show", methods={Request::METHOD_GET})
     *
     * @SWG\Tag(name="Evaluation")
     * @SWG\Response(
     *     response=200,
     *     description="Returns evaluation of given hash.",
     *     @SWG\Schema(
     *         type="array",
     *         title="evaluation",
     *         @SWG\Items(ref=@Model(type=Evaluation::class))
     *     )
     * )
     *
     * @param Evaluation|null $evaluation
     * @return JsonResponse
     */
    public function showAction(Evaluation $evaluation = null): JsonResponse
    {
        if (!$evaluation) {
            return $this->createNotFoundResponse();
        }

        return $this->createResourceResponse($evaluation);
    }

    /**
     * Add new Evaluation.
     *
     * @Route(name="api_evaluation_create", methods={Request::METHOD_POST})
     *
     * @SWG\Tag(name="Evaluation")
     * @SWG\Response(
     *     response=200,
     *     description="Add new Evaluation.",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Evaluation::class))
     *     )
     * )
     *
     * @param Request $request
     * @param Evaluation|null $evaluation
     * @return JsonResponse
     */
    public function createAction(Request $request, Evaluation $evaluation = null): JsonResponse
    {
        $data = \json_decode($request->getContent(), true);
        $message = $this->messageManager->findMessageBy(array('hash' => $data['hash']));
        $rating = $data['rating'];
        $contact = $message->getContact();
        $user = $message->getUser();
        $evaluationExist = $this->evaluationManager->findEvaluationBy(array('user' => $user, 'contact' => $contact));

        if ($evaluationExist) {
            return $this->createAlredyExistEvaluation();
        }

        if (!$user or !$contact) {
            return $this->createNotFoundResponse();
        }

        if (!$evaluation) {
            $evaluation = new Evaluation();
            $evaluation->setContact($contact);
            $evaluation->setUser($user);
            $evaluation->setMessage($message);
        }

        $form = $this->getForm(
            EvaluationType::class,
            $evaluation,
            [
                'method' => $request->getMethod(),
            ]
        );

        try {
            $this->formHandler->process($request, $form);
            $point = new Point();
            $point->setEvaluation($evaluation);
            $point->setUser($user);
            $point->setAmount((($rating-1)*5));
            $point->setType('Evaluation');
            $this->entityManager->persist($point);
            $this->entityManager->flush();
        } catch (ApiException $e) {
            return new JsonResponse($e->getData(), Response::HTTP_BAD_REQUEST);
        }
        $this->mailer->sendNewEvaluationMail($evaluation, $evaluation->getUser());
        return $this->createResourceResponse($evaluation, Response::HTTP_CREATED);
    }

    /**
     * Edit existing Evaluation.
     *
     * @Route(path="/{hash}", name="api_evaluation_edit", methods={Request::METHOD_PATCH})
     *
     * @SWG\Tag(name="Evaluation")
     * @SWG\Response(
     *     response=200,
     *     description="Updates Evaluation of given identifier and returns the updated object.",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Evaluation::class))
     *     )
     * )
     *
     * @param Request $request
     * @param Evaluation|null $evaluation
     * @return JsonResponse
     *
     * @Security("is_granted('CAN_UPDATE_USER', user)")
     */
    public function updateAction(Request $request, Evaluation $evaluation = null): JsonResponse
    {
        if (!$evaluation) {
            return $this->createNotFoundResponse();
        }

        $form = $this->getForm(
            EvaluationType::class,
            $evaluation,
            [
                'method' => $request->getMethod(),
            ]
        );

        try {
            $this->formHandler->process($request, $form);
        } catch (ApiException $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        return $this->createResourceResponse($evaluation);
    }

    /**
     * Delete Evaluation.
     *
     * @Route(path="/{hash}", name="api_evaluation_delete", methods={Request::METHOD_DELETE})
     *
     * @SWG\Tag(name="Evaluation")
     * @SWG\Response(
     *     response=200,
     *     description="Delete Evaluation of given identifier and returns the empty object.",
     * )
     *
     * @param Evaluation|null $evaluation
     * @return JsonResponse
     *
     * @Security("is_granted('CAN_DELETE_USER', user)")
     */
    public function deleteAction(Evaluation $evaluation = null): JsonResponse
    {
        if (!$evaluation) {
            return $this->createNotFoundResponse();
        }

        try {
            $evaluation->setUser(null);
            $evaluation->setContact(null);
            $evaluation->setMessage(null);
            $this->entityManager->remove($evaluation);
            $this->entityManager->flush();
        } catch (\Exception $exception) {
            return $this->createGenericErrorResponse($exception);
        }

        return $this->createSuccessfulApiResponse(self::DELETED);
    }
}

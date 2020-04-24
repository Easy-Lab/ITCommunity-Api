<?php


namespace App\Controller;

use App\Entity\Affiliate;
use App\Exception\ApiException;
use App\Form\AffiliateType;
use App\Form\Filter\AffiliateFilter;
use App\Interfaces\ControllerInterface;
use App\Service\Manager\AffiliateManager;
use App\Service\Manager\UserManager;
use App\Service\UserService;
use App\Utils\Mailer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route(path="/affiliates")
 */
class AffiliateController extends AbstractController implements ControllerInterface
{
    /**
     * @var AffiliateManager
     */
    private $affiliateManager;

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @var Mailer
     */
    private $mailer;

    /**
     * MessageController constructor.
     * @param AffiliateManager $affiliateManager
     * @param UserService $userService
     * @param Mailer $mailer
     */
    public function __construct(AffiliateManager $affiliateManager, UserService $userService, Mailer $mailer)
    {
        parent::__construct(Affiliate::class);
        $this->affiliateManager = $affiliateManager;
        $this->userService = $userService;
        $this->mailer = $mailer;
    }

    /**
     * Get all affiliates invitation.
     *
     * @Route(name="api_affiliates_list", methods={Request::METHOD_GET})
     *
     * @SWG\Tag(name="Affiliates")
     * @SWG\Response(
     *     response=200,
     *     description="Returns the list of affiliates invitation",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Affiliate::class))
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
                AffiliateFilter::class
            )
        );
    }

    /**
     * Show single affiliate invitation.
     *
     * @Route(path="/{hash}", name="api_affiliate_show", methods={Request::METHOD_GET})
     *
     * @SWG\Tag(name="Affiliates")
     * @SWG\Response(
     *     response=200,
     *     description="Returns affiliate invitation og given hash.",
     *     @SWG\Schema(
     *         type="array",
     *         title="Bug Report",
     *         @SWG\Items(ref=@Model(type=Affiliate::class))
     *     )
     * )
     *
     * @param Affiliate|null $affiliate
     * @return JsonResponse
     */
    public function showAction(Affiliate $affiliate = null): JsonResponse
    {
        if (!$affiliate) {
            return $this->createNotFoundResponse();
        }

        return $this->createResourceResponse($affiliate);
    }

    /**
     * Add new affiliate invitation.
     *
     * @Route(name="api_affiliate_create", methods={Request::METHOD_POST})
     *
     * @SWG\Tag(name="Affiliates")
     * @SWG\Response(
     *     response=200,
     *     description="Add new affiliate invitation.",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Affiliate::class))
     *     )
     * )
     *
     * @param Request $request
     * @param Affiliate|null $affiliate
     * @return JsonResponse
     *
     * @Security("is_granted('CAN_ADD_AFFILIATE', affiliate)")
     */
    public function createAction(Request $request, Affiliate $affiliate = null): JsonResponse
    {
        $user = $this->userService->getCurrentUser();

        if (!$user) {
            return $this->createNotFoundResponse();
        }

        if (!$affiliate) {
            $affiliate = new Affiliate();
            $affiliate->setUser($user);
        }

        $form = $this->getForm(
            AffiliateType::class,
            $affiliate,
            [
                'method' => $request->getMethod(),
            ]
        );

        try {
            $this->formHandler->process($request, $form);
        } catch (ApiException $e) {
            return new JsonResponse($e->getData(), Response::HTTP_BAD_REQUEST);
        }

        $this->mailer->sendInvitationMail($affiliate);

        return $this->createResourceResponse($affiliate, Response::HTTP_CREATED);
    }

    /**
     * Edit existing affiliate invitation.
     *
     * @Route(path="/{hash}", name="api_affiliate_edit", methods={Request::METHOD_PATCH})
     *
     * @SWG\Tag(name="Affiliates")
     * @SWG\Response(
     *     response=200,
     *     description="Updates affiliate invitation of given hash and returns the updated object.",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Message::class))
     *     )
     * )
     *
     * @param Request $request
     * @param Affiliate|null $affiliate
     * @return JsonResponse
     *
     * @Security("is_granted('CAN_DELETE_AFFILIATE', affiliate)")
     */
    public function updateAction(Request $request, Affiliate $affiliate = null): JsonResponse
    {
        if (!$affiliate) {
            return $this->createNotFoundResponse();
        }

        $form = $this->getForm(
            AffiliateType::class,
            $affiliate,
            [
                'method' => $request->getMethod(),
            ]
        );

        try {
            $this->formHandler->process($request, $form);
        } catch (ApiException $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        return $this->createResourceResponse($affiliate);
    }

    /**
     * Delete affiliate invitation.
     *
     * @Route(path="/{hash}", name="api_affiliate_delete", methods={Request::METHOD_DELETE})
     *
     * @SWG\Tag(name="Affiliates")
     * @SWG\Response(
     *     response=200,
     *     description="Delete affiliate invitation of given hash and returns the empty object.",
     * )
     *
     * @param Affiliate|null $affiliate
     * @return JsonResponse
     *
     * @Security("is_granted('CAN_DELETE_AFFILIATE', affiliate)")
     */
    public function deleteAction(Affiliate $affiliate = null): JsonResponse
    {
        if (!$affiliate) {
            return $this->createNotFoundResponse();
        }

        try {
            $affiliate->setUser(null);
            $this->affiliateManager->deleteAffiliate($affiliate);
        } catch (\Exception $exception) {
            return $this->createGenericErrorResponse($exception);
        }

        return $this->createSuccessfulApiResponse(self::DELETED);
    }
}

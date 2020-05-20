<?php


declare(strict_types=1);

namespace App\Controller;

use App\Entity\Picture;
use App\Entity\Review;
use App\Entity\Point;
use App\Entity\User;
use App\Exception\ApiException;
use App\Form\Filter\UserFilter;
use App\Form\UserType;
use App\Interfaces\ControllerInterface;
use App\Service\GeolocationService;
use App\Service\Manager\AffiliateManager;
use App\Service\Manager\PointManager;
use App\Service\Manager\UserManager;
use App\Service\UserService;
use App\Utils\Mailer;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/users")
 */
class UserController extends AbstractController implements ControllerInterface
{
    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * @var AffiliateManager
     */
    private $affiliateManager;

    /**
     * @var PointManager
     */
    private $pointManager;

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @var GeolocationService
     */
    protected $geolocationService;

    /**
     * @var Mailer
     */
    private $mailer;

    /**
     * UserController constructor.
     * @param UserManager $userManager
     * @param AffiliateManager $affiliateManager
     * @param PointManager $pointManager
     * @param UserService $userService
     * @param GeolocationService $geolocationService
     * @param Mailer $mailer
     */
    public function __construct(
        UserManager $userManager,
        AffiliateManager $affiliateManager,
        PointManager $pointManager,
        UserService $userService,
        GeolocationService $geolocationService,
        Mailer $mailer
    )
    {
        parent::__construct(User::class);

        $this->userManager = $userManager;
        $this->affiliateManager = $affiliateManager;
        $this->pointManager = $pointManager;
        $this->userService = $userService;
        $this->geolocationService = $geolocationService;
        $this->mailer = $mailer;
    }

    /**
     * Get all Users.
     *
     * @Route(name="api_user_list", methods={Request::METHOD_GET})
     *
     * @SWG\Tag(name="User")
     * @SWG\Response(
     *     response=200,
     *     description="Returns the list of users",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=User::class))
     *     )
     * )
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function listAction(Request $request): JsonResponse
    {
        return $this->createCollectionResponse(
            $this->handleFilterForm(
                $request,
                UserFilter::class
            )
        );
    }

    /**
     * Show single Users.
     *
     * @Route(path="/{username}", name="api_user_show", methods={Request::METHOD_GET})
     *
     * @SWG\Tag(name="User")
     * @SWG\Response(
     *     response=200,
     *     description="Returns user of given username.",
     *     @SWG\Schema(
     *         type="array",
     *         title="user",
     *         @SWG\Items(ref=@Model(type=User::class))
     *     )
     * )
     *
     * @param string $username
     * @return JsonResponse
     */
    public function showAction(string $username): JsonResponse
    {
        $user = $this->userManager->findUserByUsername($username);

        if (!$user) {
            return $this->createNotFoundResponse();
        }

        return $this->createResourceResponse($user, Response::HTTP_OK);
    }

    /**
     * Show user Reviews.
     *
     * @Route(path="/{username}/reviews", name="api_review_show", methods={Request::METHOD_GET})
     *
     * @SWG\Tag(name="User")
     * @SWG\Response(
     *     response=200,
     *     description="Returns review of given username.",
     *     @SWG\Schema(
     *         type="array",
     *         title="review",
     *         @SWG\Items(ref=@Model(type=Review::class))
     *     )
     * )
     *
     * @param string $username
     * @return JsonResponse
     */
    public function showUserReviews(string $username): JsonResponse
    {
        $user = $this->userManager->findUserByUsername($username);

        if(!$user) {
            return $this->createNotFoundResponse();
        }

        $review = $user->getReviews();


        if (!$review) {
            return $this->createNotFoundResponse();
        }

        return $this->createResourceResponse($review, Response::HTTP_OK);
    }

    /**
     * Show user Message.
     *
     * @Route(path="/{username}/messages", name="api_message_show", methods={Request::METHOD_GET})
     *
     * @SWG\Tag(name="User")
     * @SWG\Response(
     *     response=200,
     *     description="Returns message of given username.",
     *     @SWG\Schema(
     *         type="array",
     *         title="message",
     *         @SWG\Items(ref=@Model(type=Message::class))
     *     )
     * )
     *
     * @param string $username
     * @return JsonResponse
     */
    public function showUserMessage(string $username): JsonResponse
    {
        $user = $this->userManager->findUserByUsername($username);

        if(!$user) {
            return $this->createNotFoundResponse();
        }

        $message = $user->getMessages();

        if (!$message) {
            return $this->createNotFoundResponse();
        }

        return $this->createResourceResponse($message, Response::HTTP_OK);
    }

    /**
     * Show user Evaluation.
     *
     * @Route(path="/{username}/evaluations", name="api_evaluation_show", methods={Request::METHOD_GET})
     *
     * @SWG\Tag(name="User")
     * @SWG\Response(
     *     response=200,
     *     description="Returns evaluations of given username.",
     *     @SWG\Schema(
     *         type="array",
     *         title="message",
     *         @SWG\Items(ref=@Model(type=Evaluation::class))
     *     )
     * )
     *
     * @param string $username
     * @return JsonResponse
     */
    public function showUserEvaluation(string $username): JsonResponse
    {
        $user = $this->userManager->findUserByUsername($username);

        if(!$user) {
            return $this->createNotFoundResponse();
        }

        $evaluation = $user->getEvaluations();

        if (!$evaluation) {
            return $this->createNotFoundResponse();
        }

        return $this->createResourceResponse($evaluation, Response::HTTP_OK);
    }

    /**
     * Show user Point.
     *
     * @Route(path="/my/points", name="api_point_show", methods={Request::METHOD_GET})
     *
     * @SWG\Tag(name="User")
     * @SWG\Response(
     *     response=200,
     *     description="Returns point and total.",
     *     @SWG\Schema(
     *         type="array",
     *         title="message",
     *         @SWG\Items(ref=@Model(type=Point::class))
     *     )
     * )
     *
     * @return JsonResponse
     *
     * @Security("is_granted('CAN_UPDATE_USER', user)")
     */
    public function showUserPoint(): JsonResponse
    {
        $user = $this->userService->getCurrentUser();

        if(!$user) {
            return $this->createNotFoundResponse();
        }

        $points = $this->pointManager->findEvaluationsBy(array('user' => $user));
        $total = 0;

        if (!$points) {
            return $this->createNotFoundResponse();
        }

        foreach ($points as $point) {
            $total += $point->getAmount();
        }

        $jsonPoints = array("points" => $points);
        $jsonTotal = array("total_points" => $total);

        return $this->createResourceResponse(array_merge((array)$jsonPoints, $jsonTotal), Response::HTTP_OK);
    }

    /**
     * Show user Pictures.
     *
     * @Route(path="/{username}/pictures", name="api_pictures_show", methods={Request::METHOD_GET})
     *
     * @SWG\Tag(name="User")
     * @SWG\Response(
     *     response=200,
     *     description="Returns pictures of given username.",
     *     @SWG\Schema(
     *         type="array",
     *         title="picture",
     *         @SWG\Items(ref=@Model(type=Picture::class))
     *     )
     * )
     *
     * @param string $username
     * @return JsonResponse
     */
    public function showPictures(string $username): JsonResponse
    {
        $user = $this->userManager->findUserByUsername($username);

        if(!$user) {
            return $this->createNotFoundResponse();
        }

        $picture = $user->getPictures();

        if (!$picture) {
            return $this->createNotFoundResponse();
        }

        return $this->createResourceResponse($picture);
    }

    /**
     * Show user Affiliates.
     *
     * @Route(path="/{username}/affiliates", name="api_affiliates_show", methods={Request::METHOD_GET})
     *
     * @SWG\Tag(name="User")
     * @SWG\Response(
     *     response=200,
     *     description="Returns affiliates of given username.",
     *     @SWG\Schema(
     *         type="array",
     *         title="affiliate",
     *         @SWG\Items(ref=@Model(type=Affiliate::class))
     *     )
     * )
     *
     * @param string $username
     * @return JsonResponse
     */
    public function showAffiliates(string $username): JsonResponse
    {
        $user = $this->userManager->findUserByUsername($username);

        if(!$user) {
            return $this->createNotFoundResponse();
        }

        $affiliates = $user->getAffiliates();

        if (!$affiliates) {
            return $this->createNotFoundResponse();
        }

        return $this->createResourceResponse($affiliates);
    }

    /**
     * Add new User.
     *
     * @Route(name="api_user_create", methods={Request::METHOD_POST})
     *
     * @SWG\Tag(name="User")
     * @SWG\Response(
     *     response=200,
     *     description="Updates User of given identifier and returns the updated object.",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=User::class))
     *     )
     * )
     *
     * @param Request $request
     * @param User|null $user
     *
     * @return JsonResponse
     */
    public function createAction(Request $request, User $user = null): JsonResponse
    {
        $data = \json_decode($request->getContent(), true);
        $userEmailExist = $this->userManager->findUserByEmail($data['email']);
        $userUsernameExist = $this->userManager->findUserByUsername($data['username']);
        $userAffiliateExist = $this->affiliateManager->findAffiliateByEmail($data['email']);

        if ($userEmailExist or $userUsernameExist) {
            return $this->createAlredyExistResponse();
        }

        if ($userAffiliateExist) {
            $userAffiliateExist->setStatus(true);
            $userAffiliate = $userAffiliateExist->getUser();
            $point = new Point();
            $point->setUser($userAffiliate);
            $point->setAffiliate($userAffiliateExist);
            $point->setAmount(50);
            $point->setType('Affiliate');
            $this->entityManager->persist($point);
            $this->entityManager->flush();
        }

        if (!$user) {
            $user = new User();
        }

        $form = $this->getForm(
            UserType::class,
            $user,
            [
                'method' => $request->getMethod(),
            ]
        );

        try {
            if ($userAffiliateExist) {
                $point = new Point();
                $point->setUser($user);
                $point->setAffiliate($userAffiliateExist);
                $point->setAmount(15);
                $point->setType('Accepte Affiliate');
                $this->entityManager->persist($point);
                $this->entityManager->flush();
            }
            $this->geolocationService->retrieveGeocode($user);
            $this->formHandler->process($request, $form);
            $this->geolocationService->retrieveGeocode($user);
        } catch (ApiException $e) {
            return new JsonResponse($e->getData(), Response::HTTP_BAD_REQUEST);
        }

        return $this->createResourceResponse($user, Response::HTTP_CREATED);
    }

    /**
     * Send email request password.
     *
     * @Route(path="/request_password/{email}", name="api_user_request_password", methods={Request::METHOD_GET})
     *
     * @SWG\Tag(name="User")
     * @SWG\Response(
     *     response=200,
     *     description="Send request password mail.",
     *     @SWG\Schema(
     *         type="array",
     *         title="message",
     *         @SWG\Items(ref=@Model(type=User::class))
     *     )
     * )
     *
     * @param string $email
     *
     * @return JsonResponse
     */
    public function SendRequestPassword(string $email): JsonResponse
    {
        $userEmailExist = $this->userManager->findUserByEmail($email);

        try {
            if (!$userEmailExist){
                return new JsonResponse($email, Response::HTTP_NOT_FOUND);
            }
            $user = $userEmailExist;
            $this->mailer->sendRequestPasswordMail($user);
        } catch (ApiException $e) {
            return new JsonResponse($e->getData(), Response::HTTP_BAD_REQUEST);
        }

        return $this->createResourceResponse($user, Response::HTTP_CREATED);
    }

    /**
     * Edit password User.
     *
     * @Route(path="/forgot_password/{hash}", name="api_user_edit_password", methods={Request::METHOD_PATCH})
     *
     * @SWG\Tag(name="User")
     * @SWG\Response(
     *     response=200,
     *     description="Updates User of given identifier and returns the updated object.",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=User::class))
     *     )
     * )*
     *
     * @param Request $request
     * @param string $hash
     *
     * @return JsonResponse
     */
    public function updatePasswordAction(Request $request, string $hash): JsonResponse
    {
        $data = \json_decode($request->getContent(), true);

        $user = $this->userManager->findUserByHash($hash);
        $password = $data['plainPassword'];

        if (!$user) {
            return $this->createNotFoundResponse();
        }

        try {
            $user->setPlainPassword($password);
            $this->userService->encodePassword($user);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        } catch (ApiException $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        return $this->createResourceResponse($user);
    }

    /**
     * Edit existing User.
     *
     * @Route(path="/{username}", name="api_user_edit", methods={Request::METHOD_PATCH})
     *
     * @SWG\Tag(name="User")
     * @SWG\Response(
     *     response=200,
     *     description="Updates User of given identifier and returns the updated object.",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=User::class))
     *     )
     * )*
     *
     * @param Request $request
     * @param string $username
     *
     * @return JsonResponse
     *
     * @Security("is_granted('CAN_UPDATE_USER', user)")
     */
    public function updateAction(Request $request, string $username): JsonResponse
    {
        $user = $this->userManager->findUserByUsername($username);

        $data = \json_decode($request->getContent(), true);
        if (isset($data['email'])) {
            $userEmailExist = $this->userManager->findUserByEmail($data['email']);
        }
        if (isset($data['username'])) {
            $userUsernameExist = $this->userManager->findUserByUsername($data['username']);
        }

        if (isset($userEmailExist) or isset($userUsernameExist)) {
            return $this->createAlredyExistResponse();
        }

        if (!$user) {
            return $this->createNotFoundResponse();
        }

        $form = $this->getForm(
            UserType::class,
            $user,
            [
                'method' => $request->getMethod(),
            ]
        );

        try {
            $this->formHandler->process($request, $form);
        } catch (ApiException $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        return $this->createResourceResponse($user);
    }

    /**
     * Delete User.
     *
     * @Route(path="/{hash}", name="api_user_delete", methods={Request::METHOD_DELETE})
     *
     * @SWG\Tag(name="User")
     * @SWG\Response(
     *     response=200,
     *     description="Delete User of given identifier and returns the empty object.",
     * )
     *
     * @param User|null $user
     * @return JsonResponse
     *
     * @Security("is_granted('CAN_DELETE_USER', user)")
     */
    public function deleteAction(User $user = null): JsonResponse
    {
        if (!$user) {
            return $this->createNotFoundResponse();
        }

        try {
            $this->entityManager->remove($user);
            $this->entityManager->flush();
        } catch (\Exception $exception) {
            return $this->createGenericErrorResponse($exception);
        }

        return $this->createSuccessfulApiResponse(self::DELETED);
    }
}

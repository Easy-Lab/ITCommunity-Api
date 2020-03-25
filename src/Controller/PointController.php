<?php


namespace App\Controller;

use App\Entity\Point;
use App\Interfaces\ControllerInterface;
use App\Service\Manager\PointManager;
use App\Service\Manager\UserManager;
use App\Exception\ApiException;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/points")
 */
class PointController extends AbstractController implements ControllerInterface
{
    /**
     * @var PointManager
     */
    private $pointManager;

    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * MessageController constructor.
     * @param PointManager $pointManager
     * @param UserManager $userManager
     */
    public function __construct(PointManager $pointManager, UserManager $userManager)
    {
        parent::__construct(Point::class);
        $this->pointManager = $pointManager;
        $this->userManager = $userManager;
    }

    /**
     * Show top 10 users.
     *
     * @Route(name="api_points_show", methods={Request::METHOD_GET})
     *
     * @SWG\Tag(name="Point")
     * @SWG\Response(
     *     response=200,
     *     description="Returns top 10 users.",
     *     @SWG\Schema(
     *         type="object",
     *         title="point",
     *         @SWG\Items(ref=@Model(type=Point::class))
     *     )
     * )
     *
     * @return JsonResponse
     */
    public function showAction(): JsonResponse
    {
        $points = $this->pointManager->topUser();

        if (!$points) {
            return $this->createNotFoundResponse();
        }

        return $this->createResourceResponse($points, Response::HTTP_OK);
    }
}
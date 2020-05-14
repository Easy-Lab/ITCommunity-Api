<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\BugReport;
use App\Exception\ApiException;
use App\Form\BugReportType;
use App\Form\Filter\BugReportFilter;
use App\Form\Handler\BugReportStatusType;
use App\Interfaces\ControllerInterface;
use App\Utils\Mailer;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route(path="/bugrepports")
 */
class BugReportController extends AbstractController implements ControllerInterface
{
    private $mailer;

    /**
     * MessageController constructor.
     * @param Mailer $mailer
     */
    public function __construct(Mailer $mailer)
    {
        parent::__construct(BugReport::class);
        $this->mailer=$mailer;
    }

    /**
     * Get all bug reports.
     *
     * @Route(name="api_bugs_list", methods={Request::METHOD_GET})
     *
     * @SWG\Tag(name="Bug Reports")
     * @SWG\Response(
     *     response=200,
     *     description="Returns the list of bug reports",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=BugReport::class))
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
                BugReportFilter::class
            )
        );
    }

    /**
     * Show single Bug Report.
     *
     * @Route(path="/{hash}", name="api_bug_show", methods={Request::METHOD_GET})
     *
     * @SWG\Tag(name="Bug Reports")
     * @SWG\Response(
     *     response=200,
     *     description="Returns bug report og given hash.",
     *     @SWG\Schema(
     *         type="array",
     *         title="Bug Report",
     *         @SWG\Items(ref=@Model(type=BugReport::class))
     *     )
     * )
     *
     * @param BugReport|null $bugReport
     * @return JsonResponse
     */
    public function showAction(BugReport $bugReport = null): JsonResponse
    {
        if (!$bugReport) {
            return $this->createNotFoundResponse();
        }

        return $this->createResourceResponse($bugReport);
    }

    /**
     * Add new Bug Report.
     *
     * @Route(name="api_bug_create", methods={Request::METHOD_POST})
     *
     * @SWG\Tag(name="Bug Reports")
     * @SWG\Response(
     *     response=200,
     *     description="Add new bug report.",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=BugReport::class))
     *     )
     * )
     *
     * @param Request $request
     * @param BugReport|null $bugReport
     * @return JsonResponse
     */
    public function createAction(Request $request, BugReport $bugReport = null): JsonResponse
    {
        if (!$bugReport) {
            $bugReport = new BugReport();
        }

        $form = $this->getForm(
            BugReportType::class,
            $bugReport,
            [
                'method' => $request->getMethod(),
            ]
        );

        try {
            $this->formHandler->process($request, $form);
        } catch (ApiException $e) {
            return new JsonResponse($e->getData(), Response::HTTP_BAD_REQUEST);
        }
        $this->mailer->sendReportFormMail($bugReport);
        return $this->createResourceResponse($bugReport, Response::HTTP_CREATED);
    }

    /**
     * Edit existing Bug Report.
     *
     * @Route(path="/status/{hash}", name="api_bug_edit", methods={Request::METHOD_PATCH})
     *
     * @SWG\Tag(name="Bug Reports")
     * @SWG\Response(
     *     response=200,
     *     description="Updates Bug Report status of given hash and returns the updated object.",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=BugReport::class))
     *     )
     * )
     *
     * @param Request $request
     * @param BugReport|null $bugReport
     * @return JsonResponse
     *
     * @Security("is_granted('CAN_UPDATE_BUG_REPORT', bugReport)")
     */
    public function updateAction(Request $request, BugReport $bugReport = null): JsonResponse
    {
        if (!$bugReport) {
            return $this->createNotFoundResponse();
        }

        $form = $this->getForm(
            BugReportStatusType::class,
            $bugReport,
            [
                'method' => $request->getMethod(),
            ]
        );

        try {
            $this->formHandler->process($request, $form);
        } catch (ApiException $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        return $this->createResourceResponse($bugReport);
    }
}

<?php


declare(strict_types=1);

namespace App\Controller;

use App\Entity\Picture;
use App\Exception\ApiException;
use App\Form\Filter\PictureFilter;
use App\Form\PictureType;
use App\Interfaces\ControllerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/picture")
 */
class PictureController extends AbstractController implements ControllerInterface
{
    /**
     * ReviewController constructor.
     */
    public function __construct()
    {
        parent::__construct(Picture::class);
    }

    /**
     * Get all Pictures.
     *
     * @Route(name="api_picture_list", methods={Request::METHOD_GET})
     *
     * @SWG\Tag(name="Picture")
     * @SWG\Response(
     *     response=200,
     *     description="Returns the list of pictures",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Picture::class))
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
                PictureFilter::class
            )
        );
    }

    /**
     * Show single picture.
     *
     * @Route(path="/{picture}", name="api_picture_show", methods={Request::METHOD_GET})
     *
     * @SWG\Tag(name="Picture")
     * @SWG\Response(
     *     response=200,
     *     description="Returns picture of given identifier.",
     *     @SWG\Schema(
     *         type="array",
     *         title="picture",
     *         @SWG\Items(ref=@Model(type=Picture::class))
     *     )
     * )
     *
     * @param Picture|null $picture
     *
     * @return JsonResponse
     */
    public function showAction(Picture $picture = null): JsonResponse
    {
        if (!$picture) {
            return $this->createNotFoundResponse();
        }

        return $this->createResourceResponse($picture);
    }

    /**
     * Add new Picture.
     *
     * @Route(name="api_picture_create", methods={Request::METHOD_POST})
     *
     * @SWG\Tag(name="Picture")
     * @SWG\Response(
     *     response=200,
     *     description="Updates Picture of given identifier and returns the updated object.",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Picture::class))
     *     )
     * )
     *
     * @param Request $request
     * @param Picture $picture
     *
     * @return JsonResponse
     *
     * @Security("is_granted('CAN_CREATE_PICTURE', picture)")
     */
    public function createAction(Request $request, Picture $picture = null): JsonResponse
    {
        if (!$picture) {
            $picture = new Picture();
            $picture->setUser($this->getUser());
        }

        $form = $this->getForm(
            PictureType::class,
            $picture,
            [
                'method' => $request->getMethod(),
            ]
        );

        try {
            $this->formHandler->process($request, $form);
        } catch (ApiException $e) {
            return new JsonResponse($e->getData(), Response::HTTP_BAD_REQUEST);
        }

        return $this->createResourceResponse($picture, Response::HTTP_CREATED);
    }

    /**
     * Edit existing Picture.
     *
     * @Route(path="/{picture}", name="api_picture_edit", methods={Request::METHOD_PATCH})
     *
     * @SWG\Tag(name="Picture")
     * @SWG\Response(
     *     response=200,
     *     description="Updates Picture of given identifier and returns the updated object.",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Picture::class))
     *     )
     * )
     *
     * @param Request $request
     * @param Picture|null $picture
     *
     * @return JsonResponse
     *
     * @Security("is_granted('CAN_UPDATE_PICTURE', picture)")
     */
    public function updateAction(Request $request, Picture $picture = null): JsonResponse
    {
        if (!$picture) {
            return $this->createNotFoundResponse();
        }

        if (($picture->getAuthor() !== $this->getUser()) && ($this->getUser()->getRoles() === ['ROLE_USER'])) {
            return $this->createNotFoundResponse();
        }

        if($this->getUser()->getRoles() === ['ROLE_USER'] || $this->getUser()->getRoles() === ['ROLE_MODERATOR']) {
            if($picture->getAuthor()->getRoles() === ['ROLE_ADMIN']) {
                return $this->createNotFoundResponse();
            }
        }

        $form = $this->getForm(
            PictureType::class,
            $picture,
            [
                'method' => $request->getMethod(),
            ]
        );

        try {
            $this->formHandler->process($request, $form);
        } catch (ApiException $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        return $this->createResourceResponse($picture);
    }

    /**
     * Delete Picture.
     *
     * @Route(path="/{picture}", name="api_picture_delete", methods={Request::METHOD_DELETE})
     *
     * @SWG\Tag(name="Picture")
     * @SWG\Response(
     *     response=200,
     *     description="Delete Picture of given identifier and returns the empty object."
     * )
     *
     * @param Picture|null $picture
     *
     * @return JsonResponse
     *
     * @Security("is_granted('CAN_DELETE_PICTURE', picture)")
     */
    public function deleteAction(Picture $picture = null): JsonResponse
    {
        if (!$picture) {
            return $this->createNotFoundResponse();
        }

        if($this->getUser()->getRoles() === ['ROLE_USER'] || $this->getUser()->getRoles() === ['ROLE_MODERATOR']) {
            if($picture->getUser()->getRoles() === ['ROLE_ADMIN']) {
                return $this->createNotFoundResponse();
            }
        }

        try {
            $picture->setUser(null);
            $this->entityManager->remove($picture);
            $this->entityManager->flush();
        } catch (\Exception $exception) {
            return $this->createGenericErrorResponse($exception);
        }

        return $this->createSuccessfulApiResponse(self::DELETED);
    }
}

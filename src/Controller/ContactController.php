<?php


declare(strict_types=1);

namespace App\Controller;

use App\Entity\Contact;
use App\Exception\ApiException;
use App\Form\Filter\ContactFilter;
use App\Form\ContactType;
use App\Interfaces\ControllerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Service\Manager\ContactManager;

/**
 * @Route(path="/contacts")
 */
class ContactController extends AbstractController implements ControllerInterface
{
    /**
     * @var ContactManager
     */
    private $contactManager;

    /**
     * ContactController constructor.
     * @param ContactManager $contactManager
     */
    public function __construct(ContactManager $contactManager)
    {
        parent::__construct(Contact::class);
        $this->contactManager = $contactManager;
    }

    /**
     * Get all Contacts.
     *
     * @Route(name="api_contact_list", methods={Request::METHOD_GET})
     *
     * @SWG\Tag(name="Contact")
     * @SWG\Response(
     *     response=200,
     *     description="Returns list of contacts",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Contact::class))
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
                ContactFilter::class
            )
        );
    }

    /**
     * Show single Contact.
     *
     * @Route(path="/{hash}", name="api_contact_show", methods={Request::METHOD_GET})
     *
     * @SWG\Tag(name="Contact")
     * @SWG\Response(
     *     response=200,
     *     description="Returns contact of given identifier.",
     *     @SWG\Schema(
     *         type="array",
     *         title="contact",
     *         @SWG\Items(ref=@Model(type=Contact::class))
     *     )
     * )
     *
     * @param Contact|null $contact
     * @return JsonResponse
     */
    public function showAction(Contact $contact = null): JsonResponse
    {

        if (!$contact) {
            return $this->createNotFoundResponse();
        }

        return $this->createResourceResponse($contact);
    }

    /**
     * Add new Contact.
     *
     * @Route(name="api_contact_create", methods={Request::METHOD_POST})
     *
     * @SWG\Tag(name="Contact")
     * @SWG\Response(
     *     response=200,
     *     description="Add new contact.",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Contact::class))
     *     )
     * )
     *
     * @param Request $request
     * @param Contact|null $contact
     * @return JsonResponse
     *
     */
    public function createAction(Request $request, Contact $contact = null): JsonResponse
    {
        $data = \json_decode($request->getContent(), true);
        $contactEmailExist = $this->contactManager->findContactByEmail($data['email']);

        if($contactEmailExist) {
            return $this->createAlredyExistResponse();
        }

        if (!$contact) {
            $contact = new Contact();
        }

        $form = $this->getForm(
            ContactType::class,
            $contact,
            [
                'method' => $request->getMethod(),
            ]
        );

        try {
            $this->formHandler->process($request, $form);
        } catch (ApiException $e) {
            return new JsonResponse($e->getData(), Response::HTTP_BAD_REQUEST);
        }

        return $this->createResourceResponse($contact, Response::HTTP_CREATED);
    }

    /**
     * Edit existing Contact.
     *
     * @Route(path="/{hash}", name="api_contact_edit", methods={Request::METHOD_PATCH})
     *
     * @SWG\Tag(name="Contact")
     * @SWG\Response(
     *     response=200,
     *     description="Updates Contact of given identifier and returns the updated object.",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Contact::class))
     *     )
     * )
     *
     * @param Request $request
     * @param Contact|null $contact
     * @return JsonResponse
     *
     * @Security("is_granted('CAN_UPDATE_CONTACT', contact)")
     */
    public function updateAction(Request $request, Contact $contact = null): JsonResponse
    {
        if (!$contact) {
            return $this->createNotFoundResponse();
        }

        $form = $this->getForm(
            ContactType::class,
            $contact,
            [
                'method' => $request->getMethod(),
            ]
        );

        try {
            $this->formHandler->process($request, $form);
        } catch (ApiException $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        return $this->createResourceResponse($contact);
    }


    /**
     * Delete Contact.
     *
     * @Route(path="/{hash}", name="api_contact_delete", methods={Request::METHOD_DELETE})
     *
     * @SWG\Tag(name="Contact")
     * @SWG\Response(
     *     response=200,
     *     description="Delete Contact of given hash and returns the empty object."
     * )
     *
     * @param Contact|null $contact
     * @return JsonResponse
     *
     * @Security("is_granted('CAN_DELETE_CONTACT', contact)")
     */
    public function deleteAction(Contact $contact = null): JsonResponse
    {
        if (!$contact) {
            return $this->createNotFoundResponse();
        }

        try {
            $this->entityManager->remove($contact);
            $this->entityManager->flush();
        } catch (\Exception $exception) {
            return $this->createGenericErrorResponse($exception);
        }

        return $this->createSuccessfulApiResponse(self::DELETED);
    }
}
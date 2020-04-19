<?php


namespace App\Controller;

use App\Entity\ContactForm;
use App\Exception\ApiException;
use App\Form\ContactFormType;
use App\Form\Filter\ContactFormFilter;
use App\Interfaces\ControllerInterface;
use App\Utils\Mailer;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route(path="/contactforms")
 */
class ContactFormController extends AbstractController implements ControllerInterface
{
    public $mailer;
    /**
     * ContactForm constructor.
     */

    public function __construct(Mailer $mailer)
    {
        parent::__construct(ContactForm::class);
        $this->mailer=$mailer;
    }

    /**
     * Get all contact forms.
     *
     * @Route(name="api_contct_form_list", methods={Request::METHOD_GET})
     *
     * @SWG\Tag(name="Contact Forms")
     * @SWG\Response(
     *     response=200,
     *     description="Returns the list of contact fomrs",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=ContactForm::class))
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
                ContactFormFilter::class
            )
        );
    }

    /**
     * Show single Contact Form.
     *
     * @Route(path="/{hash}", name="api_contact_form_show", methods={Request::METHOD_GET})
     *
     * @SWG\Tag(name="Contact Forms")
     * @SWG\Response(
     *     response=200,
     *     description="Returns cont form of given hash.",
     *     @SWG\Schema(
     *         type="array",
     *         title="Contact Form",
     *         @SWG\Items(ref=@Model(type=ContactForm::class))
     *     )
     * )
     *
     * @param ContactForm|null $contactForm
     * @return JsonResponse
     */
    public function showAction(ContactForm $contactForm = null): JsonResponse
    {
        if (!$contactForm) {
            return $this->createNotFoundResponse();
        }

        return $this->createResourceResponse($contactForm);
    }

    /**
     * Add new Contact Form.
     *
     * @Route(name="api_contact_form_create", methods={Request::METHOD_POST})
     *
     * @SWG\Tag(name="Contact Forms")
     * @SWG\Response(
     *     response=200,
     *     description="Add new contact form.",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=ContactForm::class))
     *     )
     * )
     *
     * @param Request $request
     * @param ContactForm|null $contactForm
     * @return JsonResponse
     */
    public function createAction(Request $request, ContactForm $contactForm = null): JsonResponse
    {
        if (!$contactForm) {
            $contactForm = new ContactForm();
        }

        $form = $this->getForm(
            ContactFormType::class,
            $contactForm,
            [
                'method' => $request->getMethod(),
            ]
        );

        try {
            $this->formHandler->process($request, $form);
        } catch (ApiException $e) {
            return new JsonResponse($e->getData(), Response::HTTP_BAD_REQUEST);
        }
        $this->mailer->sendContactFormMail($contactForm);
        return $this->createResourceResponse($contactForm, Response::HTTP_CREATED);
    }

    /**
     * Edit existing Contact Form.
     *
     * @Route(path="/{hash}", name="api_contact_form_edit", methods={Request::METHOD_PATCH})
     *
     * @SWG\Tag(name="Contact Forms")
     * @SWG\Response(
     *     response=200,
     *     description="Updates Contact Form of given hash and returns the updated object.",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=ContactForm::class))
     *     )
     * )
     *
     * @param Request $request
     * @param ContactForm|null $contactForm
     * @return JsonResponse
     *
     * @Security("is_granted('CAN_UPDATE_CONTACT_FORM', contactForm)")
     */
    public function updateAction(Request $request, ContactForm $contactForm = null): JsonResponse
    {
        if (!$contactForm) {
            return $this->createNotFoundResponse();
        }

        $form = $this->getForm(
            ContactFormType::class,
            $contactForm,
            [
                'method' => $request->getMethod(),
            ]
        );

        try {
            $this->formHandler->process($request, $form);
        } catch (ApiException $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        return $this->createResourceResponse($contactForm);
    }

    /**
     * Delete Contact Form.
     *
     * @Route(path="/{hash}", name="api_contact_form_delete", methods={Request::METHOD_DELETE})
     *
     * @SWG\Tag(name="Contact Forms")
     * @SWG\Response(
     *     response=200,
     *     description="Delete contact form of given hash and returns the empty object."
     * )
     *
     * @param ContactForm|null $contactForm
     * @return JsonResponse
     *
     * @Security("is_granted('CAN_DELETE_CONTACT_FORM', contactForm)")
     */
    public function deleteAction(ContactForm $contactForm = null): JsonResponse
    {
        if (!$contactForm) {
            return $this->createNotFoundResponse();
        }

        try {
            $this->entityManager->remove($contactForm);
            $this->entityManager->flush();
        } catch (\Exception $exception) {
            return $this->createGenericErrorResponse($exception);
        }

        return $this->createSuccessfulApiResponse(self::DELETED);
    }
}

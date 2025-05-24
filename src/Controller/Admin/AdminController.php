<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Device;

use App\Entity\User;
use App\Entity\WarnMessage;
use App\Factory\CategoryFactory;
use App\Factory\DeviceFactory;
use App\Factory\EmailFactory;
use App\Factory\MessageFactory;
use App\Factory\UserFactory;
use App\Factory\WarnMessageFactory;
use App\Form\ActionDeviceType;
use App\Form\ActionUserType;
use App\Form\CategoryForm;
use App\Form\RoleUserType;
use App\Form\WarnMessageForm;
use App\Repository\CategoryRepository;
use App\Repository\DevicePictureRepository;
use App\Repository\DeviceRepository;
use App\Repository\EmailRepository;
use App\Repository\UserRepository;
use App\Repository\WarnMessageRepository;
use App\Service\Constances;
use App\Service\JWTService;
use App\Service\LoggerService;
use App\Service\MessageStandardService;
use App\Service\SendEmailService;
use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * @method User|null getUser()
 */
#[Route('/gestion/dashboard', name: 'app_admin_')]
#[isGranted('ROLE_MODERATOR')]
class AdminController extends AbstractController
{
    public function __construct(
        private readonly CategoryFactory            $categoryFactory,
        private readonly CategoryRepository         $categoryRepository,
        private readonly DeviceFactory              $deviceFactory,
        private readonly DeviceRepository           $deviceRepository,
        private readonly DevicePictureRepository    $devicePictureRepository,
        private readonly EmailFactory               $emailFactory,
        private readonly EmailRepository            $emailRepository,
        private readonly JWTService                 $JWTService,
        private readonly LoggerService              $loggerService,
        private readonly MessageStandardService     $messageStandardService,
        private readonly MessageFactory             $messageFactory,
        private readonly SendEmailService           $sendEmailService,
        private readonly UserFactory                $userFactory,
        private readonly UserRepository             $userRepository,
        private readonly WarnMessageFactory         $warnMessageFactory,
        private readonly WarnMessageRepository      $warnMessageRepository,
    ) {}

    /**
     * @throws Exception
     */
    #[Route('/', name: 'dashboard', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $this->deviceRepository->findAllNotDelete(['filters' => ['status' => Constances::VALIDED]], true);
        return $this->render('admin/home/index.html.twig', [
            'lengthDevice' => $this->deviceRepository->findAllNotDelete($request->query->all(), true),
            'lengthDevicePending' => $this->deviceRepository->findAllNotDelete(['filters' => ['status' => Constances::PENDING]], true),
            'lengthDeviceValidated' => $this->deviceRepository->findAllNotDelete(['filters' => ['status' => Constances::VALIDED]], true),
            'lengthCategory' => $this->categoryRepository->length(),
        ]);
    }


    /**
     * @throws Exception
     */
    #[Route('/devices/all', name: 'devices_all', options: ['expose' => true])]
    #[Route('/devices/pending', name: 'devices_pending', options: ['expose' => true])]
    public function allDevices(Request $request): Response
    {
        if($request->isXmlHttpRequest()){
            return $this->json($this->deviceRepository->deviceDataTableNoDelete($request->query->all()), Response::HTTP_OK, [],  ['groups' => 'device:read']);
        }

        return $this->render('admin/devices/index.html.twig', [
            'status' => $request->get('status'),
            'length' => $this->deviceRepository->findAllNotDelete($request->query->all(), true),
        ]);
    }


    /**
     * @throws TransportExceptionInterface
     * @throws \Exception
     */
    #[Route('/devices/show/{slug}', name: 'devices_show', options: ['expose' => true])]
    public function updateDevice(Request $request, Device $device): Response
    {
        $session = $request->getSession();

        $device = $this->deviceRepository->findLatestVersionByParent($device);

        $session->set('slug', $device->getSlug());

        $receiver = $device->getUser();

        $parent = $device->getParent() ?: $device;

        $form = $this->createForm(ActionDeviceType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $status = $form->get('status')->getData();
            $reason = $form->get('reason')->getData();

            if(!$reason && $status !== Constances::VALIDED){
                $session->set('statusForm', $status);
                $this->addFlash('error', 'Impossible de rejeté ou supprimé une annonce sans motif');
                return $this->redirectToRoute('app_admin_devices_show', ['slug' => $device->getSlug()]);
            }

            $session->remove('statusForm');

            $adressNoReply = $this->getParameter('adressEmailNoReply');

            $message = $form->get('message')->getData();

            $content = $message !== null ? $message:
                $this->messageStandardService
                    ->mailStandardStatusDevice($status, $device->getTitle(), $status);

            $object = "Reusix - Annonce " . $device->getTitle();

            $this->emailFactory->create($this->getUser(), $receiver, $content, $object, $adressNoReply, $device);

            $this->deviceFactory->setStatus($device, $status);

            $this->sendEmailService->send(
                $adressNoReply,
                $device->getUser()->getEmail(),
                $object,
                'action_status',
                compact('device', 'content', 'message', 'receiver', 'status')
            );
        }

        return $this->render('admin/devices/show.html.twig', [
            'status'        => $request->get('status'),
            'historics'     => $device->getParent() ? $this->deviceRepository->findBy(['parent' => $device->getParent()]) : null,
            'device'        => $device,
            'images'        => $this->devicePictureRepository->findByParent($device),
            'form'          => $form->createView(),
            'statusForm'    => $session->get('statusForm'),
            'historyEmails' => $this->emailRepository->findBy(['device' => $parent], ['id' => 'DESC']),
            'user'          => $device->getUser()
        ]);
    }

    /**
     * @throws Exception
     */
    #[Route('/devices/history/{slug}', name: 'devices_history_parent', options: ['expose' => true])]
    public function history(Request $request, Device $device): Response
    {
        if($request->isXmlHttpRequest()){

            return $this->json($this->deviceRepository->historyDevice($request->query->all(), $device), Response::HTTP_OK, [],  ['groups' => 'device:read']);
        }

        return $this->render('admin/devices/index.html.twig', [
            'status' => $request->get('status'),
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws \Exception
     */
    #[Route('/users/show/{id}', name: 'users_show', options: ['expose' => true])]
    public function showUser(Request $request, User $user): Response{

        $form = $this->createForm(ActionUserType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $action = $form->get('action')->getData();
            $reason = $form->get('reason')->getData();

            $adressNoReply = $this->getParameter('adressEmailNoReply');

            $object = 'Compte utilisateur Reusiix';

            $this->emailFactory->create($this->getUser(), $user, sprintf("Message de %s d'un compte, pour motif : \n\n \" %s \"", $action === Constances::DELETED ? 'suppression' : 'bannissement', $reason), $object, $adressNoReply);

            $this->userFactory->updateStatus($user, $action, $this->getUser());

            $this->sendEmailService->send(
                $adressNoReply,
                $user->getEmail(),
                $object,
                'action_user',
                compact('action', 'user', 'reason')
            );

            $this->addFlash('success', "Cet utlisateur vient d'être " . $action === Constances::DELETED ? 'supprimé !' : 'banni !');

            $this->redirectToRoute('app_admin_users_show', ['id' => $user->getId()]);
        }

        $formRole = $this->createForm(RoleUserType::class);

        $formRole->handleRequest($request);

        if($formRole->isSubmitted() && $formRole->isValid()){
            $this->userFactory->updateRole($user, $formRole->get('role')->getData(), $this->getUser());
        }


        return $this->render('admin/users/show.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'formRole' => $formRole->createView(),
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/users/confirm/{id}', name: 'users_confirm', options: ['expose' => true])]
    public function sendMailConfirm(User $user){

        try{
            $token = $this->JWTService->generate(['user_id' => $user->getId()]);

            $object = 'Activation de votre compte sur Reusiix';

            $this->sendEmailService->send(
                $this->getParameter('adressEmailNoReply'),
                $user->getEmail(),
                $object,
                'register',
                compact('token', 'user')
            );

            $this->emailFactory->create($this->getUser(), $user, 'Confirmation de compte', $object, $this->getParameter('adressEmailNoReply'));

            $this->addFlash('success', 'Email de validation envoyé avec succès');
        }catch (\Exception $e){
            $this->loggerService->write('error',  $e->getMessage());
        }

        return $this->redirectToRoute('app_admin_users_show', ['id' => $user->getId()]);
    }

    /**
     * @throws Exception
     */
    #[Route('/devices/user/{id}', name: 'devices_user', options: ['expose' => true])]
    public function deviceByUser(Request $request, int $id): Response
    {
        if($request->isXmlHttpRequest()){

            return $this->json($this->deviceRepository->getByUserDataTable($request->query->all(), $id), Response::HTTP_OK, [],  ['groups' => 'device:read']);
        }

        return $this->json([], Response::HTTP_NOT_FOUND);
    }


    #[Route('/users/all', name: 'users_all', options: ['expose' => true])]
    public function getUsers(Request $request): Response
    {
        if($request->isXmlHttpRequest()){
            return $this->json($this->userRepository->getAll($request->query->all(), $this->getUser()), Response::HTTP_OK, [],  ['groups' => 'user:read']);
        }

        return $this->render('admin/users/index.html.twig', [
            'length'    => $this->userRepository->length($this->getUser())
        ]);
    }


    #[Route('/categories/all', name: 'category_all', options: ['expose' => true])]
    #[Route('/categories/update/{slug}', name: 'category_update', options: ['expose' => true])]
    public function allUpdateCategories(Request $request, ?Category $category = null): Response
    {
        $form = $this->createForm(CategoryForm::class, $category ?? new Category());

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $this->categoryFactory->update($form->getData(), $this->getUser());
            $this->addFlash('success', 'Catégorie modifiée ou créée avec succès');

            return $this->redirectToRoute('app_admin_category_all');
        }
        return $this->render('admin/category/index.html.twig', [
            'form'      => $form->createView(),
            'length'    => $this->categoryRepository->length(),
            'category'  => $category,
        ]);
    }

    #[Route('/categories/datatable/', name: 'categories_datatable', options: ['expose' => true])]
    public function categoriesDatatable(Request $request): Response
    {

        if($request->isXmlHttpRequest()){
            return $this->json($this->categoryRepository->getAll($request->query->all()), Response::HTTP_OK, [],  ['groups' => 'category:read']);
        }

        return $this->json([], Response::HTTP_NOT_FOUND);
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/warn/message/', name: 'warn_message', options: ['expose' => true])]
    #[Route('/warn/message/show/{id}', name: 'warn_message_show', options: ['expose' => true])]
    public function warnMessage(Request $request, ?WarnMessage $warnMessage = null): Response
    {
        if($warnMessage){
            $form = $this->createForm(WarnMessageForm::class);

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){

                $action = $form->get('action')->getData();

                $reason = $form->get('reason')->getData();

                $authorMessage =  $warnMessage->getMessage()->getAuthor();

                $message = $warnMessage->getMessage();

                $informant = $warnMessage->getInformant();

                // modifier l'examinateur
                $this->warnMessageFactory->updateReviewer($warnMessage, $this->getUser());
                // Supprimer le message
                $this->messageFactory->delete($this->getUser(), $message);
                // bannir/suspendre utilisateur
                $this->userFactory->updateStatus(
                   $authorMessage,
                    $action,
                    $this->getUser()
                );
                // envoyer email
                $this->sendEmailService->send(
                    $this->getParameter('adressEmailNoReply'),
                    $authorMessage->getEmail(),
                    'Message signalé',
                    'warn_message',
                    compact('action', 'authorMessage', 'message', 'informant', 'reason')
                );

                $content = sprintf("%s\n - motif: %s", $action ?? 'avertissement', $reason);
                $this->emailFactory->create($this->getUser(), $authorMessage, $content, 'Message signalé', $this->getParameter('adressEmailNoReply'));

                $this->addFlash('success', 'Signalement traité avec success');

                return $this->redirectToRoute('app_admin_warn_message');
            }

            return $this->render('admin/warn/message.html.twig', [
                'form' => $form,
                'warnMessage' => $warnMessage,
            ]);

        }
        return $this->render('admin/warn/message.html.twig', []);
    }
    #[Route('/warn/message/data', name: 'warn_message_data', options: ['expose' => true])]
    public function warnMessageDatatable(Request $request): Response{

        if($request->isXmlHttpRequest()){
            $reviewed = boolval($request->query->get('reviewed'));
            return $this->json($this->warnMessageRepository->getAll($request->query->all(), $reviewed), Response::HTTP_OK, [],  ['groups' => 'warn:read']);
        }
        return $this->json([], Response::HTTP_NOT_FOUND);
    }


}


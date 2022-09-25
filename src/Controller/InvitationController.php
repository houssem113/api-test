<?php

namespace App\Controller;

use App\Core\Invitation\InvitationHandler;
use App\Core\Invitation\InvitationStatus;
use App\Entity\Invitation;
use App\Entity\User;
use App\Exception\ValidationException;
use App\Repository\InvitationRepository;
use App\Repository\UserRepository;
use App\Serializer\DataSerializer;
use App\Validator\DataValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class InvitationController extends AbstractController
{
 
    private User $user;

    public function __construct(Security $security)
    {
      
        $this->user = $security->getUser();
    }

    #[Route('/api/invitations', name: 'app_invitation', methods:["POST"])]
    public function create(
        Request $request, 
        DataSerializer $serializer, 
        DataValidator $validator,
        UserRepository $userRepo,
        EntityManagerInterface $em): JsonResponse
    {
      $data = $request->toArray();

        $invitation = $serializer->deseialize($data, Invitation::class);

        $isValid = $validator->validate($invitation);

        if(!$isValid) {

             return $this->json($validator->getErrors(), 400);
        }

        $invitedEmail = $data["invited"];

        $emailIsValid = !!filter_var($invitedEmail, FILTER_VALIDATE_EMAIL);

        if(!$emailIsValid) {

          throw new ValidationException(sprintf("This email is not valid %s", $invitedEmail));
        }

        $invited = $userRepo->findOneBy(["email" => $data["invited"]]);

        if(!$invited instanceof User) {

          throw new ValidationException(sprintf("Could not find inviter with this email %s", $invitedEmail));
        }

        $sender = $this->user;

       if($sender === $invited) {

         throw new ValidationException('Could not send invitation to the same person');
       }

        $invitation->setSender($sender);

        $invitation->setInvited($invited);

        $em->persist($invitation);

        $em->flush();

        $json = $serializer->serialize($invitation, ["groups" => ["invitation:read"]]);

        return new JsonResponse($json, 201, [], true);
    }


    #[Route('/api/invitations', name: 'app_find_inviations', methods:["GET"])]
    public function findAll(InvitationRepository $repo)
    {
      
      $user = $this->getUser();

      $invitations = $repo->findInvitations($user);

      return $this->Json($invitations, 200, [], [

         "groups" => 'invitation:read'
      ]);

    }

    #[Route('/api/invitations/handle/{id}', name: 'app_handle_invitation', methods:["PUT"])]
    
    public function cancel(Invitation $invitation, Request $request, EntityManagerInterface $em)
    {

      $actions = ["cancel", "decline", "accept"];

      $action = $request->query->get("action");

      if(!$action) {

        throw new ValidationException("Please specify an action paramater");
      }

      if(!in_array($action, $actions)) {

            throw new ValidationException(sprintf("This action '%s' is not allowed please use one of this actions [%s]", $action,implode(", ", $actions)));
      }

       $handler = new InvitationHandler($invitation, $this->user);

       $handler->CheckCancelation();
 
       if($action === "cancel") {

          $handler->cancel();
       }

       if($action === "decline") {

           $handler->decline();
       }

       if($action === "accept") {

         $handler->accept();
       }

       $em->persist($invitation);

       $em->flush();

       $message = InvitationStatus::resolve($invitation->getStatus());

       return $this->Json(["success" => sprintf("Invitation is %s", strtolower($message))]);

    }

  }

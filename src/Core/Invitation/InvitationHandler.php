<?php

namespace App\Core\Invitation;

use App\Entity\Invitation;
use App\Entity\User;
use App\Exception\UnauthorizedException;
use App\Exception\ValidationException;
use Symfony\Component\Security\Core\Security;

class InvitationHandler implements InvitationHandlerInterface
{


    public function __construct(private Invitation $invitation, private User $user)
    {

    }

    public function isInvited(): bool
    {
        return $this->user === $this->invitation->getInvited();
    }

    public function CheckCancelation(): void {

        if ($this->invitation->getStatus() === InvitationStatus::CANCELED) {

            throw new ValidationException("Invitation is already canceled");
        }
    }

    public function cancel(): self
    {

        $sender = $this->user;

        if ($sender !== $this->invitation->getSender()) {

            throw new UnauthorizedException("Could not cancel invitation because you are not the sender");
        }

        $this->invitation->setStatus(InvitationStatus::CANCELED);

        return $this;
    }


    public function decline(): self
    {

        if(!$this->isInvited())
        {
            throw new UnauthorizedException("Could not decline invitation because your are not the invited");
        }


        $this->invitation->setStatus(InvitationStatus::DECLINED);

        return $this;
        
    }


    public function accept(): self
    {

        if(!$this->isInvited())
        {
            throw new UnauthorizedException("Could not accept invitation because you are not the invited");
        }


        $this->invitation->setStatus(InvitationStatus::ACCEPTED);

        return $this;
        
    }


    
}

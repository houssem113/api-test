<?php

namespace App\Core\Invitation;

interface InvitationHandlerInterface 
{

    /**
     * Check cancelation before handling any action
     * * @throws ValidationException if invitation is canceled
     */

     public function checkCancelation(): void;
    
    /**
     * Only inviter can decline the invitation
     */
    public function decline(): self;

    /**
     * Only sender can cancel the invitation
     */
    public function cancel(): self;

    /**
     * Only invited can accept the invitation
     */
    public function accept(): self;
}
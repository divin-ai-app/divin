<?php

namespace App\Support;

use App\Enums\ClaimRequestStatus;
use App\Enums\ClaimStatus;
use App\Enums\Role;
use App\Models\ClaimRequest;
use App\Models\ProfileOwnership;

/**
 * The single code path that turns a ClaimRequest into real ownership (plan
 * §4: "ClaimRequest is the only code path that creates a ProfileOwnership
 * row"). Shared by two callers that reach "this claim is legitimate" via
 * different routes:
 *  - ClaimController::verifyOtp — the requester proved it themselves (OTP).
 *  - Admin\ClaimController::approve — staff proved it on the requester's
 *    behalf (document review), for claims where no public_email existed
 *    to OTP against.
 */
class ClaimGranter
{
    public static function grant(
        ClaimRequest $claimRequest,
        ClaimRequestStatus $finalStatus,
        string $auditAction,
        ?int $actorUserId = null,
    ): void {
        $claimRequest->update([
            'status' => $finalStatus,
            ...($actorUserId ? ['reviewed_by_staff_id' => $actorUserId] : []),
        ]);

        ProfileOwnership::query()->updateOrCreate(
            ['user_id' => $claimRequest->requested_by_user_id, 'profile_id' => $claimRequest->profile_id],
            ['role' => Role::Owner, 'granted_at' => now(), 'granted_via_claim_request_id' => $claimRequest->id],
        );

        $claimRequest->events()->create([
            'actor_user_id' => $actorUserId ?? $claimRequest->requested_by_user_id,
            'action' => $auditAction,
        ]);

        $claimRequest->profile->update(['claim_status' => ClaimStatus::Claimed]);
    }
}

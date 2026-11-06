<?php

namespace Modules\Membership\Services;

use Modules\Membership\Models\Member;
use Modules\Shared\Contracts\MemberServiceInterface;

class MemberService implements MemberServiceInterface
{
    // Anggota boleh meminjam kalau statusnya active atau borrowing (bukan suspended)
    public function isMemberActive(int $memberId): bool
    {
        $member = Member::find($memberId);

        return $member && in_array($member->status, ['active', 'borrowing'], true);
    }

    public function updateStatus(int $memberId, string $status): bool
    {
        $member = Member::find($memberId);

        if (!$member) {
            return false;
        }

        $member->update(['status' => $status]);

        return true;
    }
}
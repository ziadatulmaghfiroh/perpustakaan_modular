<?php

namespace Modules\Shared\Contracts;

interface MemberServiceInterface
{
    public function isMemberActive(int $memberId): bool;
    public function updateStatus(int $memberId, string $status): bool;
}
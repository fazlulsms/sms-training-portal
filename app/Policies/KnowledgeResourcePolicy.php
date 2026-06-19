<?php

namespace App\Policies;

use App\Models\KnowledgeResource;
use App\Models\User;

class KnowledgeResourcePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isTrainer();
    }

    public function view(User $user, KnowledgeResource $resource): bool
    {
        return $user->isAdmin() || ($user->isTrainer() && $resource->status === 'approved');
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, KnowledgeResource $resource): bool
    {
        return $user->isAdmin();
    }

    public function archive(User $user, KnowledgeResource $resource): bool
    {
        return $user->isAdmin();
    }

    public function download(User $user, KnowledgeResource $resource): bool
    {
        return $this->view($user, $resource);
    }
}

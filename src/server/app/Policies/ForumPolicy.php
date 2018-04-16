<?php

namespace Caravel\Policies;

use Riari\Forum\Policies\ForumPolicy as Base;

class ForumPolicy extends Base
{
    /**
     * Permission: Create categories.
     *
     * @param  object  $user
     * @return bool
     */
    public function createCategories($user)
    {
        return $user->roles()->where('name', 'admin')->count();
    }

    /**
     * Permission: Move categories.
     *
     * @param  object  $user
     * @return bool
     */
    public function moveCategories($user)
    {
        return $user->roles()->where('name', 'admin')->count();
    }

    /**
     * Permission: Rename categories.
     *
     * @param  object  $user
     * @return bool
     */
    public function renameCategories($user)
    {
        return $user->roles()->where('name', 'admin')->count();
    }

    /**
     * Permission: View trashed threads.
     *
     * @param  object  $user
     * @return bool
     */
    public function viewTrashedThreads($user)
    {
        return in_array($user->name, ['Moderator', 'Devel User']);
    }
}

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
        return $user->name == 'Devel User';
    }

    /**
     * Permission: Move categories.
     *
     * @param  object  $user
     * @return bool
     */
    public function moveCategories($user)
    {
        return $user->name == 'Devel User';
    }

    /**
     * Permission: Rename categories.
     *
     * @param  object  $user
     * @return bool
     */
    public function renameCategories($user)
    {
        return $user->name == 'Devel User';
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

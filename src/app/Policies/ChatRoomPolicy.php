<?php

namespace App\Policies;

use App\Models\ChatRoom;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChatRoomPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ChatRoom  $chatRoom
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, ChatRoom $chatRoom): bool
    {
        $order = $chatRoom->order;

        return $user->id === $order->user_id          // 購入者
            || $user->id === $order->item->user_id;  // 出品者
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ChatRoom  $chatRoom
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, ChatRoom $chatRoom)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ChatRoom  $chatRoom
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, ChatRoom $chatRoom)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ChatRoom  $chatRoom
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, ChatRoom $chatRoom)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ChatRoom  $chatRoom
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, ChatRoom $chatRoom)
    {
        //
    }
}

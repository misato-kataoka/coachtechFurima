<?php

namespace App\Policies;

use App\Models\Chat;
use App\Models\User;
use App\Models\Item;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChatPolicy
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
     * @param  \App\Models\Chat  $chat
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Chat $chat)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Item  $item  // ★★★ @param を追加 ★★★
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user, Item $item)
    {
        // ログインユーザーのIDが、商品の出品者IDと一致する
        $isSeller = $user->id === $item->user_id;

        // ログインユーザーのIDが、商品の購入者IDと一致する
        $isBuyer = $user->id === $item->buyer_id;

        // 出品者 または 購入者 であれば true (許可) を返す
        return $isSeller || $isBuyer;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Chat  $chat
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Chat $chat): bool
    {
        return $user->id === $chat->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Chat  $chat
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Chat $chat): bool
    {   
        // ログインユーザーのIDと、メッセージのuser_idが一致する場合のみ true (許可)
        return $user->id === $chat->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Chat  $chat
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Chat $chat)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Chat  $chat
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Chat $chat)
    {
        //
    }
}

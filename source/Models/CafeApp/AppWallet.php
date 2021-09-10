<?php

namespace Source\Models\CafeApp;

use Source\Core\Model;
use Source\Models\User;

/**
 * Class AppWallet
 * @package Source\Models\CafeApp
 */
class AppWallet extends Model
{
    /**
     * AppWallet constructor.
     */
    public function __construct()
    {
        parent::__construct("app_wallets", ["id"], ["user_id", "wallet"]);
    }

    /**
     * @param User $user
     * @return AppWallet
     */
    public function start(User $user): AppWallet
    {
        if (!$this->find("user_id = :user", "user={$user->id}")->count()) {
            $this->user_id = $user->id;
            $this->wallet = "Minha Carteira";
            $this->free = true;
            $this->save();
        }
        return $this;
    }

    /**
     * @return object
     */
    public function balance(): object
    {
        return (new AppInvoice())->balanceWallet($this);
    }
}
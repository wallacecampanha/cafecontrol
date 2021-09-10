<?php

namespace Source\Models\CafeApp;

use Source\Core\Model;
use Source\Models\User;

/**
 * Class AppOrder
 * @package Source\Models\CafeApp
 */
class AppOrder extends Model
{
    /**
     * AppOrder constructor.
     */
    public function __construct()
    {
        parent::__construct("app_orders", ["id"],
            ["user_id", "card_id", "subscription_id", "transaction", "amount", "status"]);
    }

    /**
     * @param User $user
     * @param AppCreditCard $card
     * @param AppSubscription $sub
     * @param AppCreditCard $tr
     * @return AppOrder
     */
    public function byCreditCard(User $user, AppCreditCard $card, AppSubscription $sub, AppCreditCard $tr): AppOrder
    {
        $this->user_id = $user->id;
        $this->card_id = $card->id;
        $this->subscription_id = $sub->id;
        $this->transaction = $tr->callback()->tid;
        $this->amount = number_format($tr->callback()->amount / 100, 2, ",", ".");
        $this->status = $tr->callback()->status;
        $this->save();
        return $this;
    }

    /**
     * @return mixed|Model|null
     */
    public function creditCard()
    {
        return (new AppCreditCard())->findById($this->card_id);
    }
}
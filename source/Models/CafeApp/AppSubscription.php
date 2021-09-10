<?php

namespace Source\Models\CafeApp;

use Source\Core\Model;
use Source\Models\User;

/**
 * Class AppSubscription
 * @package Source\Models\CafeApp
 */
class AppSubscription extends Model
{
    /**
     * AppSubscription constructor.
     */
    public function __construct()
    {
        parent::__construct("app_subscriptions", ["id"],
            ["user_id", "plan_id", "card_id", "status", "pay_status", "started", "due_day", "next_due"]);
    }

    /**
     * @param User $user
     * @param AppPlan $plan
     * @param AppCreditCard $card
     * @return AppSubscription
     * @throws \Exception
     */
    public function subscribe(User $user, AppPlan $plan, AppCreditCard $card): AppSubscription
    {
        $this->user_id = $user->id;
        $this->plan_id = $plan->id;
        $this->card_id = $card->id;
        $this->status = "active";
        $this->pay_status = "active";
        $this->started = date("Y-m-d");

        $day = (new \DateTime($this->started))->format("d");

        if ($day <= 28) {
            $this->due_day = $day;
            $this->next_due = date("Y-m-d", strtotime("+{$plan->period}"));
        } else {
            $due_day = 5;
            $next_due = date("Y-m-{$due_day}", strtotime("+{$plan->period}"));

            $this->due_day = $due_day;
            $this->next_due = date("Y-m-d", strtotime($next_due . "+1month"));
        }

        $this->last_charge = date("Y-m-d");
        $this->save();
        return $this;
    }

    /**
     * @return mixed|Model|null
     */
    public function user()
    {
        return (new User())->findById($this->user_id);
    }

    /**
     * @return mixed|Model|null
     */
    public function plan()
    {
        return (new AppPlan())->findById($this->plan_id);
    }

    /**
     * @return mixed|Model|null
     */
    public function creditCard()
    {
        return (new AppCreditCard())->findById($this->card_id);
    }

    /**
     * @return int
     */
    public function recurrence()
    {
        $recurrence = 0;
        $activeSubscribers = $this->find("pay_status = :s", "s=active")->fetch(true);

        if ($activeSubscribers) {
            foreach ($activeSubscribers as $subscriber) {
                $recurrence += $subscriber->plan()->price;
            }
        }

        return $recurrence;
    }

    /**
     * @return int
     */
    public function recurrenceMonth()
    {
        $recurrence = 0;
        $activeSubscribers = $this->find("pay_status = :s AND year(started) = year(now()) AND month(started) = month(now())",
            "s=active")->fetch(true);

        if ($activeSubscribers) {
            foreach ($activeSubscribers as $subscriber) {
                $recurrence += $subscriber->plan()->price;
            }
        }

        return $recurrence;
    }
}
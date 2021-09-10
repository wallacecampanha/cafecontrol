<?php

namespace Source\App\CafeApi;

use Source\Models\CafeApp\AppCreditCard;
use Source\Models\CafeApp\AppOrder;
use Source\Models\CafeApp\AppPlan;
use Source\Models\CafeApp\AppSubscription;

/**
 * Class Subscriptions
 * @package Source\App\CafeApi
 */
class Subscriptions extends CafeApi
{
    /**
     * Subscriptions constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * show subscription
     */
    public function index(): void
    {
        $subscription = (new AppSubscription())->find("user_id = :user_id AND status != :status",
            "user_id={$this->user->id}&status=canceled")->fetch();

        if (!$subscription) {
            $this->call(
                404,
                "not_found",
                "Você ainda não tem uma assinatura, sua conta é free"
            )->back();
            return;
        }

        $response["signature"] = $subscription->data();
        $response["signature"]->plan = [
            "name" => $subscription->plan()->name,
            "price" => $subscription->plan()->price,
            "period" => $subscription->plan()->period
        ];
        $response["signature"]->creditCard = [
            "brand" => $subscription->creditCard()->brand,
            "last_digits" => $subscription->creditCard()->last_digits
        ];

        $this->back($response);
    }

    /**
     * @param array $data
     * @throws \Exception
     */
    public function create(array $data): void
    {
        $request = $this->requestLimit("subscriptionsCreate", 3, 60 * 5);
        if (!$request) {
            return;
        }

        $subscription = (new AppSubscription())->find("user_id = :user_id AND status != :status",
            "user_id={$this->user->id}&status=canceled")->fetch();

        if ($subscription) {
            $this->call(
                403,
                "invalid_request",
                "Você não pode assinar pois já tem um plano ativo"
            )->back();
            return;
        }

        if (empty($data["plan_id"]) || empty($data["card_number"]) || empty($data["card_holder_name"])
            || empty($data["card_expiration_date"]) || empty($data["card_cvv"])) {
            $this->call(
                404,
                "invalid_data",
                "Informe o plano e os dados do cartão para assinar"
            )->back();
            return;
        }

        $plan_id = filter_var($data["plan_id"], FILTER_VALIDATE_INT);
        $plan = (new AppPlan())->find("id = :id AND status = :status", "id={$plan_id}&status=active")->fetch();
        if (!$plan) {
            $this->call(
                400,
                "invalid_data",
                "Você tentou assinar um plano que não existe"
            )->back();
            return;
        }

        $creditCard = new AppCreditCard();
        $card = $creditCard->creditCard(
            $this->user,
            $data["card_number"],
            $data["card_holder_name"],
            $data["card_expiration_date"],
            $data["card_cvv"]
        );

        if (!$card) {
            $this->call(
                400,
                "invalid_data",
                $creditCard->message()->getText()
            )->back();
            return;
        }

        $transaction = $card->transaction($plan->price);

        if (!$transaction) {
            $this->call(
                400,
                "invalid_data",
                $creditCard->message()->getText()
            )->back();
            return;
        }

        $subscribe = new AppSubscription();
        $subscribe->subscribe($this->user, $plan, $card);
        (new AppOrder())->byCreditCard($this->user, $card, $subscribe, $transaction);

        $this->index();
    }

    /**
     *
     */
    public function read()
    {
        $plans = (new AppPlan())->find("status = :status", "status=active")
            ->order("name")->fetch(true);

        if (!$plans) {
            $this->call(
                404,
                "not_found",
                "Não existem planos cadastrados no momento"
            )->back();
            return;
        }

        $response["plans"] = [];
        foreach ($plans as $plan) {
            $response["plans"][] = [
                "id" => $plan->id,
                "name" => $plan->name,
                "period" => $plan->period,
                "price" => $plan->price
            ];
        }

        $this->back($response);
    }

    /**
     * @param array $data
     */
    public function update(array $data)
    {
        $request = $this->requestLimit("subscriptionsUpdate", 3, 60 * 5);
        if (!$request) {
            return;
        }

        $subscription = (new AppSubscription())->find("user_id = :user_id AND status != :status",
            "user_id={$this->user->id}&status=canceled")->fetch();

        if (!$subscription) {
            $this->call(
                404,
                "not_found",
                "Para atualizar você precisa ter uma assinatura ativa"
            )->back();
            return;
        }


        if (!empty($data["plan_id"]) && $plan_id = filter_var($data["plan_id"], FILTER_VALIDATE_INT)) {
            $plan = (new AppPlan())->find("id = :id AND status = :status", "id={$plan_id}&status=active")->fetch();
            if (!$plan) {
                $this->call(
                    400,
                    "invalid_data",
                    "Você tentou assinar um plano que não existe"
                )->back();
                return;
            }

            $subscription->plan_id = $plan_id;
            $subscription->save();
        }

        if (!empty($data["card_number"]) && !empty($data["card_holder_name"])
            && !empty($data["card_expiration_date"]) && !empty($data["card_cvv"])) {

            $creditCard = new AppCreditCard();
            $card = $creditCard->creditCard(
                $this->user,
                $data["card_number"],
                $data["card_holder_name"],
                $data["card_expiration_date"],
                $data["card_cvv"]
            );

            if (!$card) {
                $this->call(
                    400,
                    "invalid_data",
                    $creditCard->message()->getText()
                )->back();
                return;
            }

            $subscription->card_id = $card->id;
            $subscription->save();

            if ($subscription->status == "past_due") {
                $transaction = $card->transaction($subscription->plan()->price);
                if ($transaction) {
                    $subscription->status = "active";
                    $subscription->next_due = date("Y-m-d",
                        strtotime($subscription->next_due . "+{$subscription->plan()->period}"));
                    $subscription->last_charge = date("Y-m-d");
                    $subscription->save();

                    (new AppOrder())->byCreditCard($this->user, $card, $subscription, $transaction);
                }
            }
        }

        $this->index();
    }
}
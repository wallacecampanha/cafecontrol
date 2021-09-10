<?php

namespace Source\Models\CafeApp;

use Source\Core\Model;
use Source\Models\User;

/**
 * Class AppCreditCard
 * @package Source\Models\CafeApp
 */
class AppCreditCard extends Model
{
    /** @var string */
    private $apiurl;

    /** @var string */
    private $apikey;

    /** @var string */
    private $endpoint;

    /** @var array */
    private $build;

    /** @var string */
    private $callback;

    /**
     * AppCreditCard constructor.
     */
    public function __construct()
    {
        parent::__construct("app_credit_cards", ["id"], ["user_id", "brand", "last_digits", "cvv", "hash"]);

        $this->apiurl = "https://api.pagar.me";
        if (CONF_PAGARME_MODE == "live") {
            $this->apikey = CONF_PAGARME_LIVE;
        } else {
            $this->apikey = CONF_PAGARME_TEST;
        }
    }

    /**
     * @param User $user
     * @param string $number
     * @param string $name
     * @param string $expDate
     * @param string $cvv
     * @return AppCreditCard|null
     */
    public function creditCard(User $user, string $number, string $name, string $expDate, string $cvv): ?AppCreditCard
    {
        $this->build = [
            "card_number" => $this->clear($number),
            "card_holder_name" => filter_var($name, FILTER_SANITIZE_STRIPPED),
            "card_expiration_date" => $this->clear($expDate),
            "card_cvv" => $this->clear($cvv)
        ];

        $this->endpoint = "/1/cards";
        $this->post();

        if (empty($this->callback->id) || !$this->callback->valid) {
            $this->message->warning("Não foi possível validar o cartão");
            return null;
        }

        $card = $this->find("user_id = :user AND hash = :hash",
            "user={$user->id}&hash={$this->callback->id}")->fetch();

        if ($card) {
            $card->cvv = $this->clear($cvv);
            $card->save();
            return $card;
        }

        $this->user_id = $user->id;
        $this->brand = $this->callback->brand;
        $this->last_digits = $this->callback->last_digits;
        $this->cvv = $this->clear($cvv);
        $this->hash = $this->callback->id;
        $this->save();

        return $this;
    }

    /**
     * @param string $amount
     * @return AppCreditCard|null
     */
    public function transaction(string $amount): ?AppCreditCard
    {
        $this->build = [
            "payment_method" => "credit_card",
            "card_id" => $this->hash,
            "amount" => $this->clear($amount)
        ];

        $this->endpoint = "/1/transactions";
        $this->post();

        if (empty($this->callback->status) || $this->callback->status != "paid") {
            $this->message->warning("Pagamento recusado pela operadora");
            return null;
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function callback()
    {
        return $this->callback;
    }

    /**
     * @param string $number
     * @return string
     */
    private function clear(string $number): string
    {
        return preg_replace("/[^0-9]/", "", $number);
    }

    /**
     *
     */
    private function post()
    {
        $url = $this->apiurl . $this->endpoint;
        $api = ["api_key" => $this->apikey];
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array_merge($this->build, $api)));
        curl_setopt($ch, CURLOPT_HTTPHEADER, []);
        $this->callback = json_decode(curl_exec($ch));
        curl_close($ch);
    }
}
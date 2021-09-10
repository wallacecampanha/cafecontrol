<?php

namespace Source\App\CafeApi;

use Source\Models\CafeApp\AppSubscription;
use Source\Models\CafeApp\AppWallet;
use Source\Support\Pager;

/**
 * Class Wallets
 * @package Source\App\CafeApi
 */
class Wallets extends CafeApi
{
    /**
     * Wallets constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     *
     */
    public function index(): void
    {
        $where = "";
        $params = "";
        $values = $this->headers;

        //by free
        if (isset($values["free"])) {
            $free = filter_var($values["free"], FILTER_VALIDATE_BOOLEAN);
            $where .= " AND free = :free";
            $params .= "&free={$free}";
        }

        $wallets = (new AppWallet())->find("user_id = :user_id{$where}", "user_id={$this->user->id}{$params}");

        if (!$wallets->count()) {
            $this->call(
                404,
                "not_found",
                "Nada encontrado para sua pesqusia. Tente outros termos"
            )->back(["count" => 0]);
            return;
        }

        $page = (!empty($values["page"]) ? $values["page"] : 1);
        $pager = new Pager(url("/wallets/"));
        $pager->pager($wallets->count(), 5, $page);

        $response["results"] = $wallets->count();
        $response["page"] = $pager->page();
        $response["pages"] = $pager->pages();

        foreach ($wallets->limit($pager->limit())->offset($pager->offset())->order("wallet")->fetch(true) as $wallet) {
            $response["wallets"][] = $wallet->data();
        }

        $this->back($response);
        return;
    }

    /**
     * @param array $data
     */
    public function create(array $data): void
    {
        $request = $this->requestLimit("walletsCreate", 5, 60);
        if (!$request) {
            return;
        }

        if (empty($data["wallet"])) {
            $this->call(
                400,
                "empty_data",
                "Para criar informe o nome de sua nova carteira"
            )->back();
            return;
        }

        //PREMIUM RESOURCE
        $subscribe = (new AppSubscription())->find("user_id = :user AND status != :status",
            "user={$this->user->id}&status=canceled");

        if (!$subscribe->count()) {
            $this->call(
                400,
                "invalid_data",
                "É preciso assinar para cadastrar uma nova carteira"
            )->back();
            return;
        }

        $wallet = new AppWallet();
        $wallet->user_id = $this->user->id;
        $wallet->wallet = filter_var($data["wallet"], FILTER_SANITIZE_STRIPPED);
        $wallet->save();

        $this->back(["wallet" => $wallet->data()]);
        return;
    }

    /**
     * @param array $data
     */
    public function read(array $data): void
    {
        if (empty($data["wallet_id"]) || !$wallet_id = filter_var($data["wallet_id"], FILTER_VALIDATE_INT)) {
            $this->call(
                400,
                "invalid_data",
                "É preciso informar o ID da carteira que deseja consultar"
            )->back();
            return;
        }

        $wallet = (new AppWallet())->find("user_id = :user_id AND id = :id",
            "user_id={$this->user->id}&id={$wallet_id}")->fetch();

        if (!$wallet) {
            $this->call(
                404,
                "not_found",
                "Você tentou acessar uma carteira que não existe"
            )->back();
            return;
        }

        $response["wallet"] = $wallet->data();
        $response["wallet"]->balance = $wallet->balance();

        $this->back($response);
    }

    /**
     * @param array $data
     */
    public function update(array $data): void
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);
        if (empty($data["wallet_id"]) || !$wallet_id = filter_var($data["wallet_id"], FILTER_VALIDATE_INT)) {
            $this->call(
                400,
                "invalid_data",
                "Informe o ID da carteira que deseja atualizar"
            )->back();
            return;
        }

        if (empty($data["wallet"])) {
            $this->call(
                400,
                "empty_data",
                "Informe um novo nome para atualizar sua carteira"
            )->back();
            return;
        }

        $wallet = (new AppWallet())->find("user_id = :user_id AND id = :id",
            "user_id={$this->user->id}&id={$wallet_id}")->fetch();

        if (!$wallet) {
            $this->call(
                404,
                "not_found",
                "Você tentou atualizar uma carteira que não existe"
            )->back();
            return;
        }

        $wallet->wallet = $data["wallet"];
        if (!$wallet->save()) {
            $this->call(
                400,
                "invalid_data",
                $wallet->message()->getText()
            )->back();
            return;
        }

        $this->back(["wallet" => $wallet->data()]);
    }

    /**
     * @param array $data
     */
    public function delete(array $data): void
    {
        if (empty($data["wallet_id"]) || !$wallet_id = filter_var($data["wallet_id"], FILTER_VALIDATE_INT)) {
            $this->call(
                400,
                "invalid_data",
                "Informe o ID da carteira que deseja deletar"
            )->back();
            return;
        }

        $wallet = (new AppWallet())->find("user_id = :user_id AND id = :id",
            "user_id={$this->user->id}&id={$wallet_id}")->fetch();

        if (!$wallet) {
            $this->call(
                404,
                "not_found",
                "Você tentou excluir uma carteira que não existe"
            )->back();
            return;
        }

        $wallet->destroy();
        $this->call(
            200,
            "success",
            "A carterira foi excluída com sucesso",
            "accepted"
        )->back();
    }
}
<?php

namespace Source\App\CafeApi;

use Source\Models\CafeApp\AppCategory;
use Source\Models\CafeApp\AppInvoice;
use Source\Models\CafeApp\AppWallet;
use Source\Support\Pager;

/**
 * Class Invoices
 * @package Source\App\CafeApi
 */
class Invoices extends CafeApi
{
    /**
     * Invoices constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * list all invoices
     */
    public function index(): void
    {
        $where = "";
        $params = "";
        $values = $this->headers;

        //by wallet
        if (!empty($values["wallet_id"]) && $wallet_id = filter_var($values["wallet_id"], FILTER_VALIDATE_INT)) {
            $where .= " AND wallet_id = :wallet_id";
            $params .= "&wallet_id={$wallet_id}";
        }

        //by type
        $typeList = ["income", "expense", "fixed_income", "fixed_expense"];
        if (!empty($values["type"]) && in_array($values["type"], $typeList) && $type = $values["type"]) {
            $where .= " AND type = :type";
            $params .= "&type={$type}";
        }

        //by status
        $statusList = ["paid", "unpaid"];
        if (!empty($values["status"]) && in_array($values["status"], $statusList) && $status = $values["status"]) {
            $where .= " AND status = :status";
            $params .= "&status={$status}";
        }

        //get invoices
        $invoices = (new AppInvoice())->find("user_id = :user_id{$where}",
            "user_id={$this->user->id}{$params}");

        if (!$invoices->count()) {
            $this->call(
                404,
                "not_found",
                "Nada encontrado para sua pesquisa. Tente outros termos"
            )->back(["results" => 0]);
            return;
        }

        $page = (!empty($values["page"]) ? $values["page"] : 1);
        $pager = new Pager(url("/invoices/"));
        $pager->pager($invoices->count(), 10, $page);

        $response["results"] = $invoices->count();
        $response["page"] = $pager->page();
        $response["pages"] = $pager->pages();

        foreach ($invoices->limit($pager->limit())->offset($pager->offset())->order("due_at ASC")->fetch(true) as $invoice) {
            $response["invoices"][] = $invoice->data();
        }

        $this->back($response);
        return;
    }

    /**
     * @param array $data
     * @throws \Exception
     */
    public function create(array $data): void
    {
        $request = $this->requestLimit("invoicesCreate", 5, 60);
        if (!$request) {
            return;
        }

        $invoice = new AppInvoice();

        if (!$invoice->launch($this->user, $data)) {
            $this->call(
                400,
                "invalid_data",
                $invoice->message()->getText()
            )->back();
            return;
        }

        $invoice->fixed($this->user, 3);
        $this->back(["invoice" => $invoice->data()]);
    }

    /**
     * @param array $data
     */
    public function read(array $data): void
    {
        if (empty($data["invoice_id"]) || !$invoice_id = filter_var($data["invoice_id"], FILTER_VALIDATE_INT)) {
            $this->call(
                400,
                "invalid_data",
                "É preciso informar o ID da fatura que deseja consultar"
            )->back();
            return;
        }

        $invoice = (new AppInvoice())->find("user_id = :user_id AND id = :id",
            "user_id={$this->user->id}&id={$invoice_id}")->fetch();

        if (!$invoice) {
            $this->call(
                404,
                "not_found",
                "Você tentou acessar uma fatura que não existe"
            )->back();
            return;
        }

        $response["invoice"] = $invoice->data();
        $response["invoice"]->wallet = (new AppWallet())->findById($invoice->wallet_id)->data();
        $response["invoice"]->category = (new AppCategory())->findById($invoice->category_id)->data();

        $this->back($response);
    }

    /**
     * @param array $data
     */
    public function update(array $data): void
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);
        if (empty($data["invoice_id"]) || !$invoice_id = filter_var($data["invoice_id"], FILTER_VALIDATE_INT)) {
            $this->call(
                400,
                "invalid_data",
                "Informe id ID do lançamento que deseja atualizar"
            )->back();
            return;
        }

        $invoice = (new AppInvoice())->find("user_id = :user_id AND id = :id",
            "user_id={$this->user->id}&id={$invoice_id}")->fetch();

        if (!$invoice) {
            $this->call(
                404,
                "not_found",
                "Você tentou atualizar um lançamento que não existe"
            )->back();
            return;
        }

        if (!empty($data["wallet_id"]) && $wallet_id = filter_var($data["wallet_id"], FILTER_VALIDATE_INT)) {
            $wallet = (new AppWallet())->find("user_id = :user_id AND id = :id",
                "user_id={$this->user->id}&id={$wallet_id}")->fetch();

            if (!$wallet) {
                $this->call(
                    400,
                    "invalid_data",
                    "Você informou uma carteira que não existe"
                )->back();
                return;
            }
        }

        if (!empty($data["category_id"]) && $category_id = filter_var($data["category_id"], FILTER_VALIDATE_INT)) {
            $category = (new AppCategory())->findById($category_id);

            if (!$category) {
                $this->call(
                    400,
                    "invalid_data",
                    "Você informou uma categoria que não existe"
                )->back();
                return;
            }
        }

        if (!empty($data["due_day"])) {
            if ($data["due_day"] < 1 || $data["due_day"] > 28) {
                $this->call(
                    400,
                    "invalid_data",
                    "O dia de vencimento deve estar entre 1 e 28"
                )->back();
                return;
            }

            $due_at = date("Y-m", strtotime($invoice->due_at)) . "-" . $data["due_day"];
        }

        $statusList = ["paid", "unpaid"];
        if (!empty($data["status"]) && !in_array($data["status"], $statusList)) {
            $this->call(
                400,
                "invalid_data",
                "O status do lançamento deve ser pago ou não pago"
            )->back();
            return;
        }

        $invoice->wallet_id = (!empty($data["wallet_id"]) ? $data["wallet_id"] : $invoice->wallet_id);
        $invoice->category_id = (!empty($data["category_id"]) ? $data["category_id"] : $invoice->category_id);
        $invoice->description = (!empty($data["description"]) ? $data["description"] : $invoice->description);
        $invoice->value = (!empty($data["value"]) ? $data["value"] : $invoice->value);
        $invoice->due_at = (!empty($due_at) ? date("Y-m-d", strtotime($due_at)) : $invoice->due_at);
        $invoice->status = (!empty($data["status"]) ? $data["status"] : $invoice->status);

        if (!$invoice->save()) {
            $this->call(
                400,
                "invalid_data",
                $invoice->message()->getText()
            )->back();
            return;
        }

        $this->back(["invoice" => $invoice->data()]);
    }

    /**
     * @param array $data
     */
    public function delete(array $data): void
    {
        if (empty($data["invoice_id"]) || !$invoice_id = filter_var($data["invoice_id"], FILTER_VALIDATE_INT)) {
            $this->call(
                400,
                "invalid_data",
                "Informe o ID do lançamento que deseja deletar"
            )->back();
            return;
        }

        $invoice = (new AppInvoice())->find("user_id = :user_id AND id = :id",
            "user_id={$this->user->id}&id={$invoice_id}")->fetch();

        if (!$invoice) {
            $this->call(
                404,
                "not_found",
                "Você tentou excluir um lançamento que não existe"
            )->back();
            return;
        }

        $invoice->destroy();
        $this->call(
            200,
            "success",
            "O lançamento foi eccluído com sucesso",
            "accpeted"
        )->back();
    }
}
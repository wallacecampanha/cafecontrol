<?php

namespace Source\Models\CafeApp;

use Source\Core\Model;
use Source\Core\Session;
use Source\Models\User;

/**
 * Class AppInvoice
 * @package Source\Models\CafeApp
 */
class AppInvoice extends Model
{
    /** @var null|int */
    public $wallet;

    /**
     * AppInvoice constructor.
     */
    public function __construct()
    {
        parent::__construct(
            "app_invoices", ["id"],
            ["user_id", "wallet_id", "category_id", "description", "type", "value", "due_at", "repeat_when"]
        );

        if ((new Session())->has("walletfilter")) {
            $this->wallet = "AND wallet_id = " . (new Session())->walletfilter;
        }
    }

    /**
     * @param User $user
     * @param array $data
     * @return AppInvoice|null
     */
    public function launch(User $user, array $data): ?AppInvoice
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        if (
            empty($data["wallet_id"]) || empty($data["category_id"]) || empty($data["description"])
            || empty($data["type"]) || empty($data["value"]) || empty($data["due_at"])
            || empty($data["repeat_when"]) || empty($data["period"]) || empty($data["enrollments"])
        ) {
            $this->message->error("Faltam dados para lançar essa fatura");
            return null;
        }

        $wallet = (new AppWallet())->find("user_id = :user_id AND id = :id",
            "user_id={$user->id}&id={$data["wallet_id"]}")->fetch();

        if (!$wallet) {
            $this->message->error("A carteira que você informou não existe");
            return null;
        }

        $category = (new AppCategory())->findById($data["category_id"]);
        if (!$category) {
            $this->message->error("A categoria que você informou não existe");
            return null;
        }

        //PREMIUM RESOURCE
        $subscribe = (new AppSubscription())->find("user_id = :user AND status != :status",
            "user={$user->id}&status=canceled");

        if (!$wallet->free && !$subscribe->count()) {
            $this->message->error("É preciso assinar para lançar nesta carteira");
            return null;
        }

        $typeList = ["income", "expense"];
        if (!in_array($data["type"], $typeList)) {
            $this->message->error("O tipo da fatura deve ser despesa ou receita");
            return null;
        }

        $check = \DateTime::createFromFormat("Y-m-d", $data["due_at"]);
        if (!$check || $check->format("Y-m-d") != $data["due_at"]) {
            $this->message->error("O vencimento da fatura não tem um formato válido");
            return null;
        }

        $repeatList = ["single", "enrollment", "fixed"];
        if (!in_array($data["repeat_when"], $repeatList)) {
            $this->message->error("A repetição da fatura deve ser única, parcelada ou fixa");
            return null;
        }

        $periodList = ["month", "year"];
        if (!in_array($data["period"], $periodList)) {
            $this->message->error("O período de cobrança da fatura deve ser mensal ou anual");
            return null;
        }

        if (!empty($data["enrollments"]) && ($data["enrollments"] < 1 || $data["enrollments"] > 420)) {
            $this->message->error("O número de parcelas da fatura deve estar entre 1 e 420");
            return null;
        }

        $status = (date($data["due_at"]) <= date("Y-m-d") ? "paid" : "unpaid");

        $this->user_id = $user->id;
        $this->wallet_id = $data["wallet_id"];
        $this->category_id = $data["category_id"];
        $this->invoice_of = null;
        $this->description = $data["description"];
        $this->type = ($data["repeat_when"] == "fixed" ? "fixed_{$data["type"]}" : $data["type"]);
        $this->value = $data["value"];
        $this->currency = "BRL";
        $this->due_at = $data["due_at"];
        $this->repeat_when = $data["repeat_when"];
        $this->period = $data["period"];
        $this->enrollments = $data["enrollments"];
        $this->enrollment_of = 1;
        $this->status = ($data["repeat_when"] == "fixed" ? "paid" : $status);

        if (!$this->save()) {
            return null;
        }

        if ($this->repeat_when == "enrollment") {
            $invoiceOf = $this->id;
            for ($enrollment = 1; $enrollment < $this->enrollments; $enrollment++) {
                $this->id = null;
                $this->invoice_of = $invoiceOf;
                $this->due_at = date("Y-m-d", strtotime($data["due_at"] . "+{$enrollment}month"));
                $this->status = (date($this->due_at) <= date("Y-m-d") ? "paid" : "unpaid");
                $this->enrollment_of = $enrollment + 1;
                $this->save();
            }
        }

        return $this;
    }

    /**
     * @param User $user
     * @param int $afterMonths
     * @throws \Exception
     */
    public function fixed(User $user, int $afterMonths = 1): void
    {
        $fixed = $this->find("user_id = :user AND status = 'paid' AND type IN('fixed_income', 'fixed_expense') {$this->wallet}",
            "user={$user->id}")->fetch(true);

        if (!$fixed) {
            return;
        }

        foreach ($fixed as $fixedItem) {
            $invoice = $fixedItem->id;
            $start = new \DateTime($fixedItem->due_at);
            $end = new \DateTime("+{$afterMonths}month");

            if ($fixedItem->period == "month") {
                $interval = new \DateInterval("P1M");
            }

            if ($fixedItem->period == "year") {
                $interval = new \DateInterval("P1Y");
            }

            $period = new \DatePeriod($start, $interval, $end);
            foreach ($period as $item) {
                $getFixed = $this->find("user_id = :user AND invoice_of = :of AND year(due_at) = :y AND month(due_at) = :m",
                    "user={$user->id}&of={$fixedItem->id}&y={$item->format("Y")}&m={$item->format("m")}",
                    "id")->fetch();

                if (!$getFixed) {
                    $newItem = $fixedItem;
                    $newItem->id = null;
                    $newItem->invoice_of = $invoice;
                    $newItem->type = str_replace("fixed_", "", $newItem->type);
                    $newItem->due_at = $item->format("Y-m-d");
                    $newItem->status = ($item->format("Y-m-d") <= date("Y-m-d") ? "paid" : "unpaid");
                    $newItem->save();
                }
            }
        }
    }

    /**
     * @param User $user
     * @param string $type
     * @param array|null $filter
     * @param int|null $limit
     * @return array|null
     */
    public function filter(User $user, string $type, ?array $filter, ?int $limit = null): ?array
    {
        $status = (!empty($filter["status"]) && $filter["status"] == "paid" ? "AND status = 'paid'" : (!empty($filter["status"]) && $filter["status"] == "unpaid" ? "AND status = 'unpaid'" : null));
        $category = (!empty($filter["category"]) && $filter["category"] != "all" ? "AND category_id = '{$filter["category"]}'" : null);

        $due_year = (!empty($filter["date"]) ? explode("-", $filter["date"])[1] : date("Y"));
        $due_month = (!empty($filter["date"]) ? explode("-", $filter["date"])[0] : date("m"));
        $due_at = "AND (year(due_at) = '{$due_year}' AND month(due_at) = '{$due_month}')";

        $due = $this->find(
            "user_id = :user AND type = :type {$status} {$category} {$due_at} {$this->wallet}",
            "user={$user->id}&type={$type}"
        )->order("day(due_at) ASC");

        if ($limit) {
            $due->limit($limit);
        }

        return $due->fetch(true);
    }

    /**
     * @return mixed|Model|null
     */
    public function wallet()
    {
        return (new AppWallet())->findById($this->wallet_id);
    }

    /**
     * @return AppCategory
     */
    public function category(): AppCategory
    {
        return (new AppCategory())->findById($this->category_id);
    }

    /**
     * @param User $user
     * @return object
     */
    public function balance(User $user): object
    {
        $balance = new \stdClass();
        $balance->income = 0;
        $balance->expense = 0;
        $balance->wallet = 0;
        $balance->balance = "positive";

        $find = $this->find("user_id = :user AND status = :status",
            "user={$user->id}&status=paid",
            "
                (SELECT SUM(value) FROM app_invoices WHERE user_id = :user AND status = :status AND type = 'income' {$this->wallet}) AS income,
                (SELECT SUM(value) FROM app_invoices WHERE user_id = :user AND status = :status AND type = 'expense' {$this->wallet}) AS expense
            ")->fetch();

        if ($find) {
            $balance->income = abs($find->income);
            $balance->expense = abs($find->expense);
            $balance->wallet = $balance->income - $balance->expense;
            $balance->balance = ($balance->wallet >= 1 ? "positive" : "negative");
        }

        return $balance;
    }

    /**
     * @param AppWallet $wallet
     * @return object
     */
    public function balanceWallet(AppWallet $wallet): object
    {
        $balance = new \stdClass();
        $balance->income = 0;
        $balance->expense = 0;
        $balance->wallet = 0;
        $balance->balance = "positive";

        $find = $this->find("user_id = :user AND status = :status",
            "user={$wallet->user_id}&status=paid",
            "
                (SELECT SUM(value) FROM app_invoices WHERE user_id = :user AND wallet_id = {$wallet->id} AND status = :status AND type = 'income') AS income,
                (SELECT SUM(value) FROM app_invoices WHERE user_id = :user AND wallet_id = {$wallet->id} AND status = :status AND type = 'expense') AS expense
            ")->fetch();

        if ($find) {
            $balance->income = abs($find->income);
            $balance->expense = abs($find->expense);
            $balance->wallet = $balance->income - $balance->expense;
            $balance->balance = ($balance->wallet >= 1 ? "positive" : "negative");
        }

        return $balance;
    }

    /**
     * @param User $user
     * @param int $year
     * @param int $month
     * @param string $type
     * @return object|null
     */
    public function balanceMonth(User $user, int $year, int $month, string $type): ?object
    {
        $onpaid = $this->find(
            "user_id = :user",
            "user={$user->id}&type={$type}&year={$year}&month={$month}",
            "
                (SELECT SUM(value) FROM app_invoices WHERE user_id = :user AND type = :type AND year(due_at) = :year AND month(due_at) = :month AND status = 'paid' {$this->wallet}) AS paid,
                (SELECT SUM(value) FROM app_invoices WHERE user_id = :user AND type = :type AND year(due_at) = :year AND month(due_at) = :month AND status = 'unpaid' {$this->wallet}) AS unpaid
            "
        )->fetch();

        if (!$onpaid) {
            return null;
        }

        return (object)[
            "paid" => str_price(($onpaid->paid ?? 0)),
            "unpaid" => str_price(($onpaid->unpaid ?? 0))
        ];
    }

    /**
     * @param User $user
     * @return object
     */
    public function chartData(User $user): object
    {
        $dateChart = [];
        for ($month = -4; $month <= 0; $month++) {
            $dateChart[] = date("m/Y", strtotime("{$month}month"));
        }

        $chartData = new \stdClass();
        $chartData->categories = "'" . implode("','", $dateChart) . "'";
        $chartData->expense = "0,0,0,0,0";
        $chartData->income = "0,0,0,0,0";

        $chart = (new AppInvoice())
            ->find("user_id = :user AND status = :status AND due_at >= DATE(now() - INTERVAL 4 MONTH) GROUP BY year(due_at) ASC, month(due_at) ASC",
                "user={$user->id}&status=paid",
                "
                    year(due_at) AS due_year,
                    month(due_at) AS due_month,
                    DATE_FORMAT(due_at, '%m/%Y') AS due_date,
                    (SELECT SUM(value) FROM app_invoices WHERE user_id = :user AND status = :status AND type = 'income' AND year(due_at) = due_year AND month(due_at) = due_month {$this->wallet}) AS income,
                    (SELECT SUM(value) FROM app_invoices WHERE user_id = :user AND status = :status AND type = 'expense' AND year(due_at) = due_year AND month(due_at) = due_month {$this->wallet}) AS expense
                "
            )->limit(5)->fetch(true);

        if ($chart) {
            $chartCategories = [];
            $chartExpense = [];
            $chartIncome = [];

            foreach ($chart as $chartItem) {
                $chartCategories[] = $chartItem->due_date;
                $chartExpense[] = $chartItem->expense;
                $chartIncome[] = $chartItem->income;
            }

            $chartData->categories = "'" . implode("','", $chartCategories) . "'";
            $chartData->expense = implode(",", array_map("abs", $chartExpense));
            $chartData->income = implode(",", array_map("abs", $chartIncome));
        }

        return $chartData;
    }
}
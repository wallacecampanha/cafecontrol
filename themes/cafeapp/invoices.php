<?php $v->layout("_theme"); ?>

<div class="app_launch_header">
    <form class="app_launch_form_filter app_form" action="<?= url("/app/filter"); ?>" method="post">
        <input type="hidden" name="filter" value="<?= $type; ?>"/>

        <select name="status">
            <option value="all" <?= (empty($filter->status) ? "selected" : ""); ?>>Todas</option>
            <option value="paid" <?= (!empty($filter->status) && $filter->status == "paid" ? "selected" : ""); ?>><?= ($type == 'income' ? "Receitas recebidas" : "Despesas pagas"); ?></option>
            <option value="unpaid" <?= (!empty($filter->status) && $filter->status == "unpaid" ? "selected" : ""); ?>><?= ($type == 'income' ? "Receitas não recebidas" : "Despesas não pagas"); ?></option>
        </select>

        <select name="category">
            <option value="all">Todas</option>
            <?php foreach ($categories as $category): ?>
                <option <?= (!empty($filter->category) && $filter->category == $category->id ? "selected" : ""); ?>
                        value="<?= $category->id; ?>"><?= $category->name; ?></option>
            <?php endforeach; ?>
        </select>

        <input list="datelist" type="text" class="radius mask-month" name="date"
               placeholder="<?= (!empty($filter->date) ? $filter->date : date("m/Y")); ?>">

        <datalist id="datelist">
            <?php for ($range = -2; $range <= 2; $range++):
                $dateRange = date("m/Y", strtotime(date("Y-m-01") . "+{$range}month"));
                ?>
                <option value="<?= $dateRange; ?>"/>
            <?php endfor; ?>
        </datalist>

        <button class="filter radius transition icon-filter icon-notext"></button>
    </form>

    <div class="app_launch_btn <?= $type; ?> radius transition icon-plus-circle"
         data-modalopen=".app_modal_<?= $type; ?>">Lançar
        <?= ($type == "income" ? "Receita" : "Despesa"); ?>
    </div>
</div>

<section class="app_launch_box">
    <?php if (!$invoices): ?>
        <?php if (empty($filter->status)): ?>
            <div class="message info icon-info">Ainda não existem contas
                a <?= ($type == "income" ? "receber" : "pagar"); ?>
                . Comece lançando suas <?= ($type == "income" ? "receitas" : "despesas"); ?>.
            </div>
        <?php else: ?>
            <div class="message info icon-info">Não existem contas
                a <?= ($type == "income" ? "receber" : "pagar"); ?>
                para o filtro aplicado.
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="app_launch_item header">
            <p class="desc">Descrição</p>
            <p class="date">Vencimento</p>
            <p class="category">Categoria</p>
            <p class="enrollment">Parcela</p>
            <p class="price">Valor</p>
        </div>
        <?php
        $unpaid = 0;
        $paid = 0;
        foreach ($invoices as $invoice):
            ?>
            <article class="app_launch_item">
                <p class="desc app_invoice_link transition">
                    <a title="<?= $invoice->description; ?>"
                       href="<?= url("/app/fatura/{$invoice->id}"); ?>"><?= str_limit_words($invoice->description,
                            3, "&nbsp;<span class='icon-info icon-notext'></span>") ?></a>
                </p>
                <p class="date">Dia <?= date_fmt($invoice->due_at, "d"); ?></p>
                <p class="category"><?= $invoice->category()->name; ?></p>
                <p class="enrollment">
                    <?php if ($invoice->repeat_when == "fixed"): ?>
                        <span class="app_invoice_link">
                            <a href="<?= url("/app/fatura/{$invoice->invoice_of}"); ?>" class="icon-exchange"
                               title="Controlar Conta Fixa">Fixa</a>
                        </span>
                    <?php elseif ($invoice->repeat_when == 'enrollment'): ?>
                        <span class="app_invoice_link">
                            <a href="<?= url("/app/fatura/{$invoice->invoice_of}"); ?>"
                               title="Controlar Parcelamento"><?= str_pad($invoice->enrollment_of, 2, 0,
                                    0); ?> de <?= str_pad($invoice->enrollments, 2, 0, 0); ?></a>
                        </span>
                    <?php else: ?>
                        <span class="icon-calendar-check-o">Única</span>
                    <?php endif; ?>
                </p>
                <p class="price">
                    <span>R$</span>
                    <span><?= str_price($invoice->value); ?></span>
                    <?php if ($invoice->status == 'unpaid'): $unpaid += $invoice->value; ?>
                        <span class="check <?= $type; ?> icon-thumbs-o-down transition"
                              data-toggleclass="active icon-thumbs-o-down icon-thumbs-o-up"
                              data-onpaid="<?= url("/app/onpaid"); ?>"
                              data-date="<?= ($filter->date ?? date("m/Y")); ?>"
                              data-invoice="<?= $invoice->id; ?>"></span>
                    <?php else: $paid += $invoice->value; ?>
                        <span class="check <?= $type; ?> icon-thumbs-o-up transition"
                              data-toggleclass="active icon-thumbs-o-down icon-thumbs-o-up"
                              data-onpaid="<?= url("/app/onpaid"); ?>"
                              data-date="<?= ($filter->date ?? date("m/Y")); ?>"
                              data-invoice="<?= $invoice->id; ?>"></span>
                    <?php endif; ?>
                </p>
            </article>
        <?php endforeach; ?>

        <div class="app_launch_item footer">
            <p class="icon-thumbs-o-down j_total_unpaid">R$ <?= str_price($unpaid); ?></p>
            <p class="icon-thumbs-o-up j_total_paid">R$ <?= str_price($paid); ?></p>
        </div>
    <?php endif; ?>
</section>

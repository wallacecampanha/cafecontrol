<?php $v->layout("_theme"); ?>

<div class="app_main_right" style="margin: 0;">
    <ul class="app_widget_shortcuts">
        <li class="income radius transition" data-modalopen=".app_modal_income">
            <p class="icon-plus-circle">Receita</p>
        </li>
        <li class="expense radius transition" data-modalopen=".app_modal_expense">
            <p class="icon-plus-circle">Despesa</p>
        </li>
    </ul>
</div>

<section class="app_launch_box">
    <?php if (!$invoices): ?>
        <div class="message info icon-info">Ainda não existem contas a fixas. Comece lançando
            suas recorrências.
        </div>
    <?php else: ?>
        <div class="app_launch_item header">
            <p class="desc">Descrição</p>
            <p class="date">Vencimento</p>
            <p class="category">Categoria</p>
            <p class="enrollment">Status</p>
            <p class="price">Valor</p>
        </div>
        <?php
        $unpaid = 0;
        $paid = 0;
        foreach ($invoices as $invoice):
            ?>
            <article class="app_launch_item">
                <p class="desc app_invoice_link transition">
                    <a title="<?= $invoice->description; ?>" href="<?= url("/app/fatura/{$invoice->id}"); ?>">
                        <?= ($invoice->type == "fixed_income" ? "Receita / " : "Despesa / "); ?>
                        <?= str_limit_words($invoice->description, 3,
                            "&nbsp;<span class='icon-info icon-notext'></span>") ?>
                    </a>
                </p>
                <p class="date">Dia <?= date_fmt($invoice->due_at, "d"); ?></p>
                <p class="category"><?= $invoice->category()->name; ?></p>
                <p class="enrollment"><?= ($invoice->status == "paid" ? "Ativa" : "Invativa"); ?></p>
                <p class="price">
                    <span>R$</span>
                    <span><?= str_price($invoice->value); ?></span>
                    <span><?= ($invoice->period == "month" ? "/mês" : "/ano"); ?></span>
                </p>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>
</section>

<?php $v->layout("_theme"); ?>

<div class="app_formbox app_widget">
    <form class="app_form" action="<?= url("/app/invoice/{$invoice->id}"); ?>" method="post">
        <input type="hidden" name="update" value="true"/>

        <label>
            <span class="field icon-leanpub">Descrição:</span>
            <input class="radius" type="text" name="description" value="<?= $invoice->description; ?>" required/>
        </label>

        <div class="label_group">
            <label>
                <span class="field icon-money">Valor:</span>
                <input class="radius mask-money" type="text" name="value" value="<?= $invoice->value; ?>" required/>
            </label>

            <label>
                <span class="field icon-filter">Dia de vencimento:</span>
                <input class="radius masc-date" type="number" name="due_day" name="value"
                       min="1" max="<?= date("t", strtotime($invoice->due_at)); ?>"
                       value="<?= date_fmt($invoice->due_at, "d"); ?>" required/>
            </label>
        </div>

        <label>
            <span class="field icon-briefcase">Carteira:</span>
            <select name="wallet">
                <?php foreach ($wallets as $wallet): ?>
                    <option <?= ($wallet->id == $invoice->wallet_id ? "selected" : ""); ?>
                            value="<?= $wallet->id; ?>">&ofcir; <?= $wallet->wallet; ?></option>
                <?php endforeach; ?>
            </select>
        </label>

        <div class="label_group">
            <label>
                <span class="field icon-filter">Categoria:</span>
                <select name="category">
                    <?php foreach ($categories as $category): ?>
                        <option <?= ($category->id == $invoice->category_id ? "selected" : ""); ?>
                                value="<?= $category->id; ?>">&ofcir; <?= $category->name; ?></option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label>
                <span class="field icon-filter">Status:</span>
                <select name="status">
                    <?php if ($invoice->type == "fixed_income" || $invoice->type == "fixed_expense"): ?>
                        <option <?= ($invoice->status != 'paid' ?: "selected"); ?> value="paid">&ofcir; Ativa</option>
                        <option <?= ($invoice->status != 'unpaid' ?: "selected"); ?> value="unpaid">&ofcir; Inativa
                        </option>
                    <?php else: ?>
                        <option <?= ($invoice->status == 'paid' ? "selected" : ""); ?> value="paid">
                            &ofcir; <?= ($invoice->type == 'income' ? "Recebida" : "Paga"); ?></option>
                        <option <?= ($invoice->status == 'unpaid' ? "selected" : ""); ?> value="unpaid">
                            &ofcir; <?= ($invoice->type == 'income' ? "Não recebida" : "Não paga"); ?></option>
                    <?php endif; ?>
                </select>
            </label>
        </div>

        <div class="al-center">
            <div class="app_formbox_actions">
                <span data-invoiceremove="<?= url("/app/remove/{$invoice->id}"); ?>"
                      class="btn_remove transition icon-error">Excluir</span>
                <button class="btn btn_inline radius transition icon-pencil-square-o">Atualizar</button>
                <a class="btn_back transition radius icon-sign-in" href="<?= url_back(); ?>" title="Voltar">Voltar</a>
            </div>
        </div>
    </form>
</div>
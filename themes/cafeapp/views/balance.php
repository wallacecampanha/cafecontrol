<div class="balance <?= ($invoice->type == "income" ? "positive" : "negative"); ?>">
    <p class="desc">
        <b class="app_invoice_link transition">
            <a title="<?= $invoice->description; ?>"
               href="<?= url("app/fatura/{$invoice->id}"); ?>">
                <?= str_limit_words($invoice->description, 1, "&nbsp;<span class='icon-info icon-notext'></span>"); ?>
            </a></b>

        <?php
        $now = new DateTime();
        $due = new DateTime($invoice->due_at);
        $expire = $now->diff($due);
        $s = ($expire->days == 1 ? "" : "s");

        if (!$expire->days && $expire->invert):?>
            <span class="date" style="color: var(--color-yellow);">Hoje</span>
        <?php elseif (!$expire->invert): ?>
            <span class="date">Em <?= ($expire->days <= 1 ? "1 dia" : "{$expire->days} dias") ?></span>
        <?php else: ?>
            <span class="date"
                  style="color: var(--color-red);">Há <?= ($expire->days <= 1 ? "1 dia" : "{$expire->days} dias"); ?></span>
        <?php endif; ?>
    </p>
    <p class="price">
        R$&nbsp;<?= str_price($invoice->value); ?>

        <?php if ($invoice->status == 'unpaid'): ?>
            <span class="check <?= $invoice->type; ?> icon-thumbs-o-down transition"
                  data-toggleclass="active icon-thumbs-o-down icon-thumbs-o-up"
                  data-onpaid="<?= url("/app/onpaid"); ?>"
                  data-invoice="<?= $invoice->id; ?>"></span>
        <?php else: ?>
            <span class="check <?= $invoice->type; ?> icon-thumbs-o-up transition"
                  data-toggleclass="active icon-thumbs-o-down icon-thumbs-o-up"
                  data-onpaid="<?= url("/app/onpaid"); ?>"
                  data-invoice="<?= $invoice->id; ?>"></span>
        <?php endif; ?>
    </p>
</div>
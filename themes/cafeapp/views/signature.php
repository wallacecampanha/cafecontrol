<form action="<?= $action; ?>" method="post" class="app_form">
    <?php if ($plans): ?>
        <div class="label_check al-center">
            <?php
            $checked = 0;
            foreach ($plans as $plan):
                $checked++;
                ?>
                <label class="<?= ($checked == 1 ? "check" : ""); ?>" data-checkbox="true">
                    <input type="radio" name="plan"
                           value="<?= $plan->id; ?>" <?= ($checked == 1 ? "checked" : ""); ?> >
                    <?= $plan->name; ?> R$ <?= str_price($plan->price); ?>/<?= $plan->period_str; ?>
                </label>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <label>
        <span class="field">Número do cartão:</span>
        <input class="radius mask-card" name="card_number" type="tel" required
               placeholder="&bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull;"/>
    </label>

    <label>
        <span class="field">Nome do títular:</span>
        <input class="radius" name="card_holder_name" type="text" required
               placeholder="Igual ao impresso no cartão"/>
    </label>

    <div class="label_group">
        <label>
            <span class="field">Data de expiração:</span>
            <input class="radius mask-month" name="card_expiration_date" type="text" required
                   placeholder="mm/yyyy"/>
        </label>

        <label>
            <span class="field">CVV:</span>
            <input class="radius" name="card_cvv" type="number" required
                   placeholder="&bull;&bull;&bull;"/>
        </label>
    </div>

    <button class="btn radius transition icon-check-square-o"><?= ($btn ?? "Confirmar Pagamento"); ?></button>
</form>
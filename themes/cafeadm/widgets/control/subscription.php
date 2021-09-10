<?php $v->layout("_admin"); ?>
<?php $v->insert("widgets/control/sidebar.php"); ?>

<section class="dash_content_app">
    <header class="dash_content_app_header">
        <h2 class="icon-star-o">Assinatura #<?= str_pad($subscription->id, 3, 0, 0); ?>
            de <?= $subscription->user()->fullName(); ?></h2>
    </header>

    <div class="dash_content_app_box">
        <form class="app_form" action="<?= url("/admin/control/subscription/{$subscription->id}"); ?>" method="post">
            <!--ACTION SPOOFING-->
            <input type="hidden" name="action" value="update"/>

            <div class="label_g2">
                <label class="label">
                    <span class="legend">*Plano:</span>
                    <select name="plan_id" required>
                        <?php foreach ($plans as $plan):
                            $plan_id = $subscription->plan()->id;
                            $selected = function ($value) use ($plan_id) {
                                return ($plan_id == $value ? "selected" : "");
                            };
                            ?>
                            <option <?= $selected($plan->id); ?> value="<?= $plan->id; ?>"><?= $plan->name; ?> -
                                R$ <?= str_price($plan->price); ?>/<?= $plan->period_str; ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label class="label">
                    <span class="legend">*Cartão:</span>
                    <select name="card_id" required>
                        <?php if ($cards): ?>
                            <?php foreach ($cards as $card):
                                $card_id = $subscription->creditCard()->id;
                                $selected = function ($value) use ($card_id) {
                                    return ($card_id == $value ? "selected" : "");
                                };
                                ?>
                                <option <?= $selected($card->id); ?> value="<?= $card->id; ?>">
                                    Cartão final <?= $card->last_digits; ?> (<?= str_title($card->brand); ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option disabled value="">ERRO: Cliente sem cartão cadastrado</option>
                        <?php endif; ?>
                    </select>
                </label>
            </div>

            <div class="label_g2">
                <label class="label">
                    <span class="legend">*Status da assinatura:</span>
                    <select name="status" required>
                        <?php
                        $status = $subscription->status;
                        $selected = function ($value) use ($status) {
                            return ($status == $value ? "selected" : "");
                        };
                        ?>
                        <option <?= $selected("active"); ?> value="active">Ativa</option>
                        <option <?= $selected("past_due"); ?> value="past_due">Atrasada</option>
                        <option <?= $selected("canceled"); ?> value="canceled">Cancelada
                        </option>
                    </select>
                </label>

                <label class="label">
                    <span class="legend">*Status da recorrência:</span>
                    <select name="pay_status" required>
                        <?php
                        $pay_status = $subscription->pay_status;
                        $selected = function ($value) use ($pay_status) {
                            return ($pay_status == $value ? "selected" : "");
                        };
                        ?>
                        <option <?= $selected("active"); ?> value="active">Ativa</option>
                        <option <?= $selected("canceled"); ?> value="canceled">Cancelada</option>
                    </select>
                </label>
            </div>

            <label class="label">
                <span class="legend">*Dia de vencimento:</span>
                <select name="due_day" required>
                    <?php for ($day = 1; $day <= 28; $day++):
                        $due_day = $subscription->due_day;
                        $selected = function ($value) use ($due_day) {
                            return ($due_day == $value ? "selected" : "");
                        };
                        ?>
                        <option <?= $selected($day); ?> value="<?= $day; ?>">
                            Todo dia <?= str_pad($day, 2, 0, 0); ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </label>

            <div class="label_g2">
                <label class="label">
                    <span class="legend">*Próximo vencimento:</span>
                    <input class="mask-date" type="text" name="next_due"
                           value="<?= date("d/m/Y", strtotime($subscription->next_due)); ?>" required/>
                </label>

                <label class="label">
                    <span class="legend">*Útima cobrança:</span>
                    <input class="mask-date" type="text" name="last_charge"
                           value="<?= date("d/m/Y", strtotime($subscription->last_charge)); ?>" required/>
                </label>
            </div>

            <div class="al-right">
                <button class="btn btn-blue icon-check-square-o">Atualizar Assinatura</button>
            </div>
        </form>
    </div>
</section>
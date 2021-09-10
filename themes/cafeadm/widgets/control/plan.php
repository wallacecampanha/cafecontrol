<?php $v->layout("_admin"); ?>
<?php $v->insert("widgets/control/sidebar.php"); ?>

<section class="dash_content_app">
    <?php if (!$plan): ?>
        <header class="dash_content_app_header">
            <h2 class="icon-plus-circle">Novo Plano</h2>
        </header>

        <div class="dash_content_app_box">
            <form class="app_form" action="<?= url("/admin/control/plan"); ?>" method="post">
                <!--ACTION SPOOFING-->
                <input type="hidden" name="action" value="create"/>

                <div class="label_g2">
                    <label class="label">
                        <span class="legend">*Plano:</span>
                        <input type="text" name="name" placeholder="Nome do plano" required/>
                    </label>

                    <label class="label">
                        <span class="legend">*Preço:</span>
                        <input class="mask-money" type="text" name="price" required/>
                    </label>
                </div>

                <div class="label_g2">
                    <label class="label">
                        <span class="legend">*Período:</span>
                        <select name="period" required>
                            <option value="1month">Mensal</option>
                            <option value="1year">Anual</option>
                        </select>
                    </label>

                    <label class="label">
                        <span class="legend">*Inf. de período:</span>
                        <select name="period_str" required>
                            <option value="mês">Mês</option>
                            <option value="ano">Ano</option>
                        </select>
                    </label>
                </div>

                <label class="label">
                    <span class="legend">*Status:</span>
                    <select name="status" required>
                        <option value="active">Ativa</option>
                        <option value="inactive">Inativa</option>
                    </select>
                </label>

                <div class="al-right">
                    <button class="btn btn-green icon-check-square-o">Criar Plano</button>
                </div>
            </form>
        </div>
    <?php else: ?>
        <header class="dash_content_app_header">
            <h2 class="icon-pencil-square-o">Editar Plano</h2>
        </header>

        <div class="dash_content_app_box">
            <form class="app_form" action="<?= url("/admin/control/plan/{$plan->id}"); ?>" method="post">
                <!--ACTION SPOOFING-->
                <input type="hidden" name="action" value="update"/>

                <div class="label_g2">
                    <label class="label">
                        <span class="legend">*Plano:</span>
                        <input type="text" name="name" value="<?= $plan->name; ?>" required/>
                    </label>

                    <label class="label">
                        <span class="legend">*Preço:</span>
                        <input class="mask-money" type="text" name="price"
                               value="<?= str_price($plan->price); ?>" required/>
                    </label>
                </div>

                <div class="label_g2">
                    <label class="label">
                        <span class="legend">*Período:</span>
                        <select name="period" required>
                            <?php
                            $period = $plan->period;
                            $selected = function ($value) use ($period) {
                                return ($period == $value ? "selected" : "");
                            };
                            ?>
                            <option <?= $selected("1month"); ?> value="1month">Mensal</option>
                            <option <?= $selected("1year"); ?> value="1year">Anual</option>
                        </select>
                    </label>

                    <label class="label">
                        <span class="legend">*Inf. de período:</span>
                        <select name="period_str" required>
                            <?php
                            $period = $plan->period_str;
                            $selected = function ($value) use ($period) {
                                return ($period == $value ? "selected" : "");
                            };
                            ?>
                            <option <?= $selected("mês"); ?> value="mês">Mês</option>
                            <option <?= $selected("ano"); ?> value="ano">Ano</option>
                        </select>
                    </label>
                </div>

                <label class="label">
                    <span class="legend">*Status:</span>
                    <select name="status" required>
                        <?php
                        $status = $plan->status;
                        $selected = function ($value) use ($status) {
                            return ($status == $value ? "selected" : "");
                        };
                        ?>
                        <option <?= $selected("active"); ?> value="active">Ativo</option>
                        <option <?= $selected("inactive"); ?> value="inactive">Inativo</option>
                    </select>
                </label>

                <div class="app_form_footer">
                    <button class="btn btn-blue icon-check-square-o">Atualizar</button>
                    <?php if (!$subscribers): ?>
                        <a href="#" class="remove_link icon-error"
                           data-post="<?= url("/admin/control/plan"); ?>"
                           data-action="delete"
                           data-confirm="Tem certeza que deseja excluir este plano?"
                           data-plan_id="<?= $plan->id; ?>">Excluir Plano</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    <?php endif; ?>
</section>

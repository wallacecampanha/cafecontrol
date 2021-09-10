<?php $v->layout("_admin"); ?>
<?php $v->insert("widgets/control/sidebar.php"); ?>

<section class="dash_content_app">
    <header class="dash_content_app_header">
        <h2 class="icon-flag">Planos</h2>
        <a class="icon-plus-circle btn btn-green" href="<?= url("/admin/control/plan"); ?>">Novo Plano</a>
    </header>

    <div class="dash_content_app_box">
        <section>
            <div class="app_control_plans">
                <?php if (!$plans): ?>
                    <div class="message info icon-info">Ainda não existem planos cadastrados.</div>
                <?php else: ?>
                    <?php foreach ($plans as $plan): ?>
                        <article class="radius">
                            <div>
                                <h4 class="icon-flag"><?= $plan->name; ?></h4>
                                <p><b>Assinantes:</b> <?= str_pad($plan->subscribers()->count(), 3, 0, 0); ?></p>
                                <p><b>Recorrência:</b> R$ <?= str_price($plan->recurrence()); ?></p>
                            </div>

                            <div>
                                <p><b>Período:</b> <?= str_title($plan->period_str); ?></p>
                                <p><b>Preço:</b> R$ <?= str_price($plan->price); ?></p>
                                <p><b>Status:</b> <?= ($plan->status == "active" ? "Ativo" : "Inativo"); ?></p>
                            </div>

                            <div class="actions">
                                <a class="icon-pencil btn btn-blue" title=""
                                   href="<?= url("/admin/control/plan/{$plan->id}"); ?>">Atualizar</a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <?= $paginator; ?>
        </section>
    </div>
</section>
<?php $v->layout("_admin"); ?>
<?php $v->insert("widgets/control/sidebar.php"); ?>

<section class="dash_content_app">
    <header class="dash_content_app_header">
        <h2 class="icon-coffee">Control</h2>
    </header>

    <div class="dash_content_app_box">
        <div class="app_control_home">
            <section class="app_control_home_stats">
                <article class="radius">
                    <h4 class="icon-user">Assinantes</h4>
                    <p><?= str_pad($stats->subscriptions, 5, 0, 0); ?></p>
                </article>

                <article class="radius">
                    <h4 class="icon-user-plus">Por 30 dias</h4>
                    <p><?= str_pad($stats->subscriptionsMonth, 5, 0, 0); ?></p>
                </article>

                <article class="radius">
                    <h4 class="icon-calendar-check-o">Este mês:</h4>
                    <p>R$ <?= str_price($stats->recurrenceMonth); ?></p>
                </article>

                <article class="radius">
                    <h4 class="icon-retweet">Recorrência:</h4>
                    <p>R$ <?= str_price($stats->recurrence); ?></p>
                </article>
            </section>


            <section class="app_control_subs radius">
                <h3 class="icon-heartbeat">Assinaturas:</h3>
                <?php if (!$subscriptions): ?>
                    <div class="message info icon-info">Ainda não existem assinantes em seu APP, assim que eles
                        começarem a chegar você verá os mais recentes aqui. Esperamos que seja em breve :)
                    </div>
                <?php else: ?>
                    <?php foreach ($subscriptions as $subscription): ?>
                        <article class="subscriber">
                            <h5><?= date_fmt($subscription->created_at, "d.m.y \- H\hm"); ?>
                                - <?= $subscription->user()->fullName(); ?></h5>
                            <p><?= $subscription->plan()->name; ?> -
                                R$ <?= str_price($subscription->plan()->price) . "/{$subscription->plan()->period_str}"; ?></p>
                            <p><?= ($subscription->status == "active" ? "Ativa" : "Inativa"); ?></p>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </section>
        </div>
    </div>
</section>
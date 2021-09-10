<?php $v->layout("_admin"); ?>
<?php $v->insert("widgets/control/sidebar.php"); ?>

<section class="dash_content_app">
    <header class="dash_content_app_header">
        <h2 class="icon-star"><?= (empty($search) ? "Assinaturas" : "Pesquisa por {$search}"); ?></h2>
        <form action="<?= url("/admin/control/subscriptions"); ?>" method="post" class="app_search_form">
            <input type="text" name="s" value="<?= $search; ?>" placeholder="Pesquisar Assinante:">
            <button class="icon-search icon-notext"></button>
        </form>
    </header>

    <div class="dash_content_app_box">
        <section>
            <div class="app_control_subscribers">
                <?php if (!$subscriptions): ?>
                    <?php if (empty($search)): ?>
                        <div class="message info icon-info">Ainda não existem assinantes em seu APP, assim que eles
                            começarem a chegar você verá os mais recentes aqui. Esperamos que seja em breve :)
                        </div>
                    <?php else: ?>
                        <div class="message warning icon-warning">Não foram encontrados assinantes com NOME, SOBRENOME
                            ou EMAIL igual a <b><?= $search; ?></b>. Você pode tentar outros termos...
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <?php foreach ($subscriptions as $subscription):
                        $photo = $subscription->user()->photo();
                        $userPhoto = ($photo ? image($photo, 300, 300) :
                            theme("/assets/images/avatar.jpg", CONF_VIEW_ADMIN));
                        ?>
                        <article class="radius">
                            <div class="cover" style="background-image: url(<?= $userPhoto; ?>);"></div>
                            <h4><?= $subscription->user()->fullName() ?></h4>
                            <p class="email"><?= $subscription->user()->email; ?></p>
                            <p class="info">
                                Assina o plano <?= $subscription->plan()->name; ?><br>
                                de R$&nbsp;<?= str_price($subscription->plan()->price); ?>
                                por <?= $subscription->plan()->period_str; ?><br>
                                desde <?= date_fmt($subscription->started, "d.m.y"); ?>
                            </p>
                            <div class="actions">
                                <a class="icon-cog btn btn-blue" title=""
                                   href="<?= url("/admin/control/subscription/{$subscription->id}"); ?>">Gerenciar</a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <?= $paginator; ?>
        </section>
    </div>
</section>
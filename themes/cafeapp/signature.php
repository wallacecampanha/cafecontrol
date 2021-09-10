<?php $v->layout("_theme"); ?>

<?php if (!empty($subscription)): ?>
    <article class="app_signature app_signature_me radius">
        <?php if ($subscription->status == "past_due"): ?>
            <div class="message warning icon-warning">
                Importante <?= user()->first_name; ?>: Não foi possível cobrar seu cartão e sua assinatura está
                atrasada, para não perder os recursos PRO é preciso regularizar seu pagamento.
                <p style="margin-top: 20px">Cadastre um novo cartão de crédito <span data-go=".payment" class="message_btn">CLICANDO AQUI</span></p>
            </div>
        <?php endif; ?>

        <header class="app_signature_me_header">
            <h1>Minha assinatura:</h1>
            <p>Confira detalhes da sua assinatura</p>
        </header>

        <ul class="app_signature_detail radius">
            <li><span>Status:</span> <span><?= ($subscription->status == "active" ? "Ativa" : "Atrasada"); ?></span>
            </li>
            <li><span>Plano:</span> <span><?= $subscription->plan()->name; ?></span></li>
            <li><span>Início:</span> <span><?= date_fmt($subscription->started, "d/m/Y"); ?></span></li>
            <li><span>Valor:</span> <span>R$ <?= str_price($subscription->plan()->price); ?></span></li>
            <li><span>Cartão:</span>
                <span style="text-transform: uppercase"><?= $subscription->creditCard()->brand; ?> Final <?= $subscription->creditCard()->last_digits; ?></span>
            </li>
            <li><span>Próximo pagamento:</span> <span><?= date_fmt($subscription->next_due, "d/m/Y"); ?></span></li>
        </ul>

        <div class="app_signature_me_header">
            <h2>Meus Pagamentos:</h2>
            <p>Confira detalhes de seus pagamentos</p>
        </div>

        <div class="app_signature_orders">
            <?php if (empty($orders)): ?>
                <div class="message info icon-info al-center">Ainda não existem faturas para sua assinatura. Quando
                    existirem, você poderá conferi-las aqui.
                </div>
            <?php else: ?>
                <div class="app_signature_orders_item title">
                    <p>Data</p>
                    <p>Valor</p>
                    <p>Cartão</p>
                </div>
                <?php foreach ($orders as $order):
                    $status = ($order->status == "paid" ? '<span class="icon-check" title="Paga"></span>' : ($order->status == "waiting" ? '<span class="icon-clock-o" title="Aguardando Pagamento"></span>' : '<span class="icon-error" title="Recusada"></span>')); ?>
                    <article class="app_signature_orders_item">
                        <p>
                            <?= $status; ?> <?= date_fmt($order->created_at, "d/m/Y"); ?>
                            <sup>#<?= $order->transaction; ?></sup>
                        </p>
                        <p>R$ <?= str_price($order->amount); ?></p>
                        <p style="text-transform: uppercase;"><?= $order->creditCard()->brand; ?>
                            Final <?= $order->creditCard()->last_digits; ?></p>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="app_signature_me_header payment">
            <h2>Meio de pagamento:</h2>
            <p>Precisa usar um novo cartão de crédito?</p>
        </div>

        <div class="app_signature_pay_card">
            <?php $v->insert("views/signature",
                ["plans" => null, "action" => url("/pay/update"), "btn" => "Cadastrar Cartão"]); ?>
        </div>
    </article>
<?php else: ?>
    <article class="app_signature radius">
        <header class="app_signature_header gradient gradient-green">
            <span class="icon icon-coffee icon-notext"></span>
            <h2>Seja PRO por apenas R$ 0,16 centavos por dia e controle tudo!</h2>
            <p>Crie multiplas carteiras para controlar suas finanças PF, PJ, contas bancárias, cartões de crédito,
                poupanças... e libere o controle absoluto de suas contas.</p>
        </header>

        <section class="app_signature_resources">
            <div class="app_signature_resources_overflow">
                <h3>Compare as versões FREE e PRO e entenda!</h3>
                <div class="app_signature_resources_item title">
                    <p class="resouce">Recurso</p>
                    <p class="check icon-user-plus">FREE</p>
                    <p class="check icon-coffee">PRO</p>
                </div>
                <article class="app_signature_resources_item">
                    <p class="resouce">Contas a receber</p>
                    <p class="check icon-check icon-notext"></p>
                    <p class="check icon-check icon-notext"></p>
                </article>
                <article class="app_signature_resources_item">
                    <p class="resouce">Contas a pagar</p>
                    <p class="check icon-check icon-notext"></p>
                    <p class="check icon-check icon-notext"></p>
                </article>
                <article class="app_signature_resources_item">
                    <p class="resouce">Parcelamento</p>
                    <p class="check icon-check icon-notext"></p>
                    <p class="check icon-check icon-notext"></p>
                </article>
                <article class="app_signature_resources_item">
                    <p class="resouce">Contas a fixas</p>
                    <p class="check icon-check icon-notext"></p>
                    <p class="check icon-check icon-notext"></p>
                </article>
                <article class="app_signature_resources_item">
                    <p class="resouce">Carteiras ilimitadas</p>
                    <p class="check icon-error icon-notext"></p>
                    <p class="check icon-check icon-notext"></p>
                </article>
                <article class="app_signature_resources_item">
                    <p class="resouce">Vencimentos por e-mail</p>
                    <p class="check icon-error icon-notext"></p>
                    <p class="check icon-check icon-notext"></p>
                </article>
                <article class="app_signature_resources_item">
                    <p class="resouce">PF, PJ, cartões, etc</p>
                    <p class="check icon-error icon-notext"></p>
                    <p class="check icon-check icon-notext"></p>
                </article>
                <article class="app_signature_resources_item">
                    <p class="resouce">Filtro por fonte (carteira)</p>
                    <p class="check icon-error icon-notext"></p>
                    <p class="check icon-check icon-notext"></p>
                </article>
                <article class="app_signature_resources_item">
                    <p class="resouce">Controle de saldo geral</p>
                    <p class="check icon-error icon-notext"></p>
                    <p class="check icon-check icon-notext"></p>
                </article>
            </div>
        </section>

        <article class="app_signature_pay">
            <?php if (!$plans): ?>
                <div class="message info al-center">Desculpe <?= user()->first_name; ?>, mas no momento não existem
                    planos
                    para assinatura :/
                </div>
            <?php else: ?>
                <header>
                    <h2 class="icon-coffee">Assine o PRO</h2>
                    <p>E libere todos os recursos do CaféApp</p>
                </header>

                <div class="app_signature_pay_card">
                    <?php $v->insert("views/signature", ["plans" => $plans, "action" => url("/pay/create")]); ?>
                </div>
            <?php endif; ?>
        </article>
    </article>
<?php endif; ?>
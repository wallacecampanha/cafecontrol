<?php $v->layout("_theme"); ?>

<div class="ajax_response"></div>

<section class="app_wallets">
    <article class="wallet wallet_add radius gradient-blue">
        <h2 class="icon-info icon-notext"></h2>
        <h3>Crie e gerencie suas carteiras</h3>
        <p>Organize suas contas de diferentes fontes como <b>Minha Casa</b> para PF, <b>Minha Empresa</b> para PJ, ou
            ainda <b>Cartão 6431</b> para organizar cartões. Controle tudo...</p>
        <span class="btn wallet_action radius transition icon-plus-circle">Adicionar Carteira</span>

        <div class="wallet_overlay radius" style="background-color: var(--color-blue)">
            <div>
                <p>Insira o nome da sua nova carteira, como <b>Minha Casa</b>, <b>Minha Empresa</b>, <b>Cartão 5546</b>...
                    e tudo pronto!</p>
                <form action="<?= url("/app/wallets/new"); ?>" method="post" autocomplete="off">
                    <input type="text" name="wallet_name" placeholder="Ex: Casa, Empresa, Cartão 5546" required/>
                    <button class="form_btn icon-check gradient radius transition gradient-blue gradient-hover">Abrir
                        Carteira
                    </button>
                </form>
                <span class="wallet_overlay_close icon-sign-out transition">fechar</span>
            </div>
        </div>

    </article>

    <?php foreach ($wallets as $wallet): $balance = $wallet->balance() ?>
        <article class="wallet radius <?= ($balance->balance == "positive" ? "gradient-green" : "gradient-red"); ?>">
            <span class="wallet_remove wallet_action icon-times-circle icon-notext"></span>
            <h2 class="icon-briefcase icon-notext"></h2>
            <input data-walletedit="<?= url("/app/wallets/{$wallet->id}"); ?>" type="text" name="wallet"
                   value="<?= $wallet->wallet; ?>">
            <p class="wallet_balance">R$ <?= str_price($balance->wallet); ?></p>
            <p class="wallet_income">Receitas: R$ <?= str_price($balance->income); ?></p>
            <p class="wallet_expense">Despesas: R$ <?= str_price($balance->expense); ?></p>

            <div class="wallet_overlay radius">
                <div>
                    <h3 class="icon-warning">ATENÇÃO:</h3>
                    <p>Ao deletar uma carteira todos as contas lançadas nesta serão perdidas. Tem certeza que deseja
                        deletar a carteira?</p>
                    <span data-walletremove="<?= url("/app/wallets/{$wallet->id}"); ?>"
                          data-wallet="<?= $wallet->id; ?>"
                          class="btn radius transition icon-warning">Sim, DELETAR!</span>

                    <span class="wallet_overlay_close icon-sign-out transition">fechar</span>
                </div>
            </div>
        </article>
    <?php endforeach; ?>
</section>

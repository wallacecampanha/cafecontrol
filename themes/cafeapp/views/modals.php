<div class="app_modal" data-modalclose="true">
    <!--INCOME-->
    <?php
    $user = user();
    $wallets = (new \Source\Models\CafeApp\AppWallet())
        ->find("user_id = :u", "u={$user->id}", "id, wallet")
        ->order("wallet")
        ->fetch(true);

    $v->insert("views/invoice", [
        "type" => "income",
        "wallets" => $wallets,
        "categories" => (new \Source\Models\CafeApp\AppCategory())
            ->find("type = :t", "t=income", "id, name")
            ->order("order_by, name")
            ->fetch(true)
    ]);

    $v->insert("views/invoice", [
        "type" => "expense",
        "wallets" => $wallets,
        "categories" => (new \Source\Models\CafeApp\AppCategory())
            ->find("type = :t", "t=expense", "id, name")
            ->order("order_by, name")
            ->fetch(true)
    ]);
    ?>

    <!--SUPPORT-->
    <div class="app_modal_box app_modal_contact">
        <p class="title icon-calendar-minus-o">Fale conosco:</p>
        <form class="app_form" action="<?= url("/app/support"); ?>" method="post">
            <label>
                <span class="field icon-life-ring">O que precisa?</span>
                <select name="subject" required>
                    <option value="Pedido de suporte">&ofcir; Preciso de suporte</option>
                    <option value="Nova sugestão">&ofcir; Enviar uma sugestão</option>
                    <option value="Nova reclamação">&ofcir; Enviar uma reclamação</option>
                    <option value="Mudança de plano">&ofcir; Mudar meu plano</option>
                    <option value="Pedido de cancelamento">&ofcir; Cancelar assinatura</option>
                </select>
            </label>

            <label>
                <span class="field icon-comments-o">Mensagem:</span>
                <textarea class="radius" name="message" rows="4" required></textarea>
            </label>

            <button class="btn radius transition icon-paper-plane-o">Enviar Agora</button>
        </form>
    </div>
</div>
<?php $v->layout("_theme"); ?>

<article class="auth">
    <div class="auth_content container content">
        <header class="auth_header">
            <h1>Criar nova senha</h1>
            <p>Informe e repita uma nova senha para recuperar seu acesso.</p>
        </header>

        <form class="auth_form" action="<?= url("/recuperar/resetar"); ?>" method="post"
              enctype="multipart/form-data">

            <div class="ajax_response"><?= flash(); ?></div>
            <input type="hidden" name="code" value="<?= $code; ?>"/>
            <?= csrf_input(); ?>

            <label>
                <div>
                    <span class="icon-envelope">Nova Senha:</span>
                    <span><a title="Voltar e entrar!" href="<?= url("/entrar"); ?>">Voltar e entrar!</a></span>
                </div>
                <input type="password" name="password" placeholder="Nova senha:" required/>
            </label>

            <label>
                <div class="unlock-alt"><span class="icon-envelope">Repita a nova senha:</span></div>
                <input type="password" name="password_re" placeholder="Repita a nova senha:" required/>
            </label>

            <button class="auth_form_btn transition gradient gradient-green gradient-hover">Alterar Senha</button>
        </form>
    </div>
</article>
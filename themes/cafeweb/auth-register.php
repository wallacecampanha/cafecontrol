<?php $v->layout("_theme"); ?>

<article class="auth">
    <div class="auth_content container content">
        <header class="auth_header">
            <h1>Cadastre-se</h1>
            <p>Já tem uma conta? <a title="Fazer login!" href="<?= url("/entrar"); ?>">Fazer login!</a></p>
        </header>

        <form class="auth_form" action="<?= url("/cadastrar"); ?>" method="post" enctype="multipart/form-data">
            <div class="ajax_response"><?= flash(); ?></div>
            <?= csrf_input(); ?>

            <label>
                <div><span class="icon-user">Nome:</span></div>
                <input type="text" name="first_name" placeholder="Primeiro nome:" required/>
            </label>

            <label>
                <div><span class="icon-user-plus">Sobrenome:</span></div>
                <input type="text" name="last_name" placeholder="Último nome:" required/>
            </label>

            <label>
                <div><span class="icon-envelope">Email:</span></div>
                <input type="email" name="email" placeholder="Informe seu e-mail:" required/>
            </label>

            <label>
                <div class="unlock-alt"><span class="icon-unlock-alt">Senha:</span></div>
                <input type="password" name="password" placeholder="Informe sua senha:" required/>
            </label>

            <button class="auth_form_btn transition gradient gradient-green gradient-hover">Criar conta</button>
        </form>
    </div>
</article>
<?php $v->layout("_admin"); ?>
<?php $v->insert("widgets/users/sidebar.php"); ?>

<section class="dash_content_app">
    <header class="dash_content_app_header">
        <h2 class="icon-user">Usuários</h2>
        <form action="<?= url("/admin/users/home"); ?>" class="app_search_form">
            <input type="text" name="s" value="<?= $search; ?>" placeholder="Pesquisar Usuário:">
            <button class="icon-search icon-notext"></button>
        </form>
    </header>

    <div class="dash_content_app_box">
        <section>
            <div class="app_users_home">
                <?php foreach ($users as $user):
                    $userPhoto = ($user->photo() ? image($user->photo, 300, 300) :
                        theme("/assets/images/avatar.jpg", CONF_VIEW_ADMIN));
                    ?>
                    <article class="user radius">
                        <div class="cover" style="background-image: url(<?= $userPhoto; ?>)"></div>
                        <?php if ($user->level >= 5): ?>
                            <p class="level icon-life-ring">ADMIN</p>
                        <?php else: ?>
                            <p class="level icon-user">USUÁRIO</p>
                        <?php endif; ?>

                        <h4><?= $user->fullName(); ?></h4>
                        <div class="info">
                            <p><?= $user->email; ?></p>
                            <p>Desde <?= date_fmt($user->created_at, "d/m/y \à\s H\hi"); ?></p>
                        </div>

                        <div class="actions">
                            <a class="icon-cog btn btn-blue" href="<?= url("/admin/users/user/{$user->id}"); ?>"
                               title="">Gerenciar</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <?= $paginator; ?>
        </section>
    </div>
</section>
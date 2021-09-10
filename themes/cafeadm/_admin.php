<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">

    <?= $head; ?>

    <link rel="stylesheet" href="<?= url("/shared/styles/boot.css"); ?>"/>
    <link rel="stylesheet" href="<?= url("/shared/styles/styles.css"); ?>"/>
    <link rel="stylesheet" href="<?= theme("/assets/css/style.css", CONF_VIEW_ADMIN); ?>"/>

    <link rel="icon" type="image/png" href="<?= theme("/assets/images/favicon.png", CONF_VIEW_ADMIN); ?>"/>
</head>
<body>

<div class="ajax_load" style="z-index: 999;">
    <div class="ajax_load_box">
        <div class="ajax_load_box_circle"></div>
        <p class="ajax_load_box_title">Aguarde, carregando...</p>
    </div>
</div>

<div class="ajax_response"><?= flash(); ?></div>

<div class="dash">
    <aside class="dash_sidebar">
        <article class="dash_sidebar_user">
            <?php
            $photo = user()->photo();
            $userPhoto = ($photo ? image($photo, 300, 300) : theme("/assets/images/avatar.jpg", CONF_VIEW_ADMIN));
            ?>
            <div><img class="dash_sidebar_user_thumb" src="<?= $userPhoto; ?>" alt="" title=""/></div>
            <h3 class="dash_sidebar_user_name">
                <a href="<?= url("/admin/users/user/" . user()->id); ?>"><?= user()->fullName(); ?></a>
            </h3>
        </article>

        <ul class="dash_sidebar_nav">
            <?php
            $nav = function ($icon, $href, $title) use ($app) {
                $active = (explode("/", $app)[0] == explode("/", $href)[0] ? "active" : null);
                $url = url("/admin/{$href}");
                return "<li class=\"dash_sidebar_nav_li {$active}\"><a class=\"icon-{$icon}\" href=\"{$url}\">{$title}</a></li>";
            };

            echo $nav("home", "dash", "Dashboard");
            echo $nav("coffee", "control/home", "Control");
            echo $nav("pencil-square-o", "blog/home", "Blog");
            echo $nav("comments-o", "faq/home", "FAQs");
            echo $nav("user", "users/home", "Usuários");
            echo "<li class=\"dash_sidebar_nav_li\"><a class=\"icon-link\" href=\"" . url() . " \" target=\"_blank\">Ver site</a></li>";

            echo $nav("sign-out on_mobile", "logoff", "Sair");
            ?>
        </ul>
    </aside>
    <section class="dash_content">
        <div class="dash_userbar">
            <div class="dash_userbar_box">
                <div class="dash_content_box">
                    <h1 class="icon-cog transition"><a href="<?= url("/admin/dash"); ?>">Café<b>Admin</b></a></h1>
                    <div class="dash_userbar_box_bar">
                        <span class="notification_center_open icon-bell"
                              data-count="<?= url("/admin/notifications/count"); ?>"
                              data-notify="<?= url("/admin/notifications/list"); ?>">0</span>
                        <span class="no_mobile icon-clock-o"><?= date("d/m H\hi"); ?></span>
                        <a class="no_mobile icon-sign-out" title="Sair" href="<?= url("/admin/logoff"); ?>">Sair</a>
                        <span class="icon-menu icon-notext mobile_menu transition"></span>
                    </div>
                </div>
            </div>

            <div class="notification_center"></div>
        </div>

        <div class="dash_content_box">
            <?= $v->section("content"); ?>
        </div>
    </section>
</div>

<script src="<?= url("/shared/scripts/jquery.min.js"); ?>"></script>
<script src="<?= url("/shared/scripts/jquery.form.js"); ?>"></script>
<script src="<?= url("/shared/scripts/jquery-ui.js"); ?>"></script>
<script src="<?= url("/shared/scripts/jquery.mask.js"); ?>"></script>
<script src="<?= url("/shared/scripts/tinymce/tinymce.min.js"); ?>"></script>
<script src="<?= theme("/assets/js/scripts.js", CONF_VIEW_ADMIN); ?>"></script>
<?= $v->section("scripts"); ?>

</body>
</html>
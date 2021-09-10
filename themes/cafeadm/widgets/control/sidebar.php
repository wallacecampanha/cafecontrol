<div class="dash_content_sidebar">
    <h3 class="icon-asterisk">dashboard/control</h3>
    <p class="dash_content_sidebar_desc">Planos, assinaturas e gestão do CaféControl? Está tudo aqui...</p>

    <nav>
        <?php
        $nav = function ($icon, $href, $title) use ($app) {
            $active = ($app == $href ? "active" : null);
            $url = url("/admin/{$href}");
            return "<a class=\"icon-{$icon} radius {$active}\" href=\"{$url}\">{$title}</a>";
        };

        echo $nav("coffee", "control/home", "Control");
        echo $nav("star", "control/subscriptions", "Assinaturas");
        echo $nav("flag", "control/plans", "Planos");
        ?>
    </nav>
</div>
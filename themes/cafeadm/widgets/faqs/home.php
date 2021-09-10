<?php $v->layout("_admin"); ?>
<?php $v->insert("widgets/faqs/sidebar.php"); ?>

<section class="dash_content_app">
    <header class="dash_content_app_header">
        <h2 class="icon-comments-o">FAQs</h2>
        <a class="icon-plus-circle btn btn-green" href="<?= url("/admin/faq/channel"); ?>">Novo Canal</a>
    </header>

    <div class="dash_content_app_box">
        <section>
            <div class="app_faqs_home">
                <?php if (!$channels): ?>
                    <div class="message info icon-info">Ainda não existem canais de FAQ cadastrados.</div>
                <?php else: ?>
                    <?php foreach ($channels as $channel): ?>
                        <article class="radius">
                            <header>
                                <h3><?= $channel->channel; ?></h3>
                                <p><?= $channel->description; ?></p>
                                <div>
                                    <a href="<?= url("/admin/faq/channel/{$channel->id}"); ?>"
                                       class="icon-pencil btn btn-blue">Editar Canal</a>
                                </div>
                                <a href="<?= url("/admin/faq/question/{$channel->id}"); ?>"
                                   class="icon-plus-circle btn btn-green">Nova Pergunta</a>
                            </header>
                            <div>
                                <?php
                                $channelId = $channel->id;
                                $edit = function ($id) use ($channelId) {
                                    $url = url("/admin/faq/question/{$channelId}/{$id}");
                                    return "<a href=\"{$url}\" class=\"btn btn-blue icon-pencil icon-notext\"></a>";
                                };
                                ?>

                                <?php if (!$channel->questions()->count()): ?>
                                    <div class="message info icon-info al-center">
                                        Ainda não existem perguntas
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($channel->questions()->fetch(true) as $question): ?>
                                        <div class="question radius">
                                            <?= $edit($question->id); ?> - <?= $question->question; ?>
                                        </div>
                                    <?php endforeach; ?>

                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <?= $paginator; ?>
        </section>
    </div>
</section>
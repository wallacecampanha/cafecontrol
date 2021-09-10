<?php $v->layout("_admin"); ?>
<?php $v->insert("widgets/faqs/sidebar.php"); ?>

<section class="dash_content_app">
    <?php if (!$channel): ?>
        <header class="dash_content_app_header">
            <h2 class="icon-plus-circle">Novo Canal</h2>
        </header>

        <div class="dash_content_app_box">
            <form class="app_form" action="<?= url("/admin/faq/channel"); ?>" method="post">
                <!--ACTION SPOOFING-->
                <input type="hidden" name="action" value="create"/>

                <label class="label">
                    <span class="legend">*Canal:</span>
                    <input type="text" name="channel" placeholder="Nome do canal" required/>
                </label>

                <label class="label">
                    <span class="legend">*Descrição:</span>
                    <textarea name="description" rows="3" placeholder="Sobre esse canal" required></textarea>
                </label>

                <div class="al-right">
                    <button class="btn btn-green icon-check-square-o">Criar Canal</button>
                </div>
            </form>
        </div>
    <?php else: ?>
        <header class="dash_content_app_header">
            <h2 class="icon-comments-o"><?= $channel->channel; ?></h2>
            <a href="<?= url("/admin/faq/question/{$channel->id}"); ?>"
               class="icon-plus-circle btn btn-green">Nova Pergunta</a>
        </header>

        <div class="dash_content_app_box">
            <form class="app_form" action="<?= url("/admin/faq/channel/{$channel->id}"); ?>" method="post">
                <!--ACTION SPOOFING-->
                <input type="hidden" name="action" value="update"/>

                <label class="label">
                    <span class="legend">*Canal:</span>
                    <input type="text" name="channel" value="<?= $channel->channel; ?>" placeholder="Nome do canal"
                           required/>
                </label>

                <label class="label">
                    <span class="legend">*Descrição:</span>
                    <textarea name="description" rows="3" placeholder="Sobre esse canal"
                              required><?= $channel->description; ?></textarea>
                </label>

                <div class="app_form_footer">
                    <button class="btn btn-blue icon-check-square-o">Atualizar</button>
                    <a href="#" class="remove_link icon-error"
                       data-post="<?= url("/admin/faq/channel/{$channel->id}"); ?>"
                       data-action="delete"
                       data-confirm="Tem certeza que deseja excluir este canal e todas as suas perguntas e respostas?"
                       data-plan_id="<?= $channel->id; ?>">Excluir Canal</a>
                </div>
            </form>
        </div>
    <?php endif; ?>
</section>
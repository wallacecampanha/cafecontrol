<?php $v->layout("_admin"); ?>
<?php $v->insert("widgets/faqs/sidebar.php"); ?>

<section class="dash_content_app">
    <?php if (!$question): ?>
        <header class="dash_content_app_header">
            <h2 class="icon-plus-circle">Nova Pergunta</h2>
        </header>

        <div class="dash_content_app_box">
            <form class="app_form" action="<?= url("/admin/faq/question/{$channel->id}"); ?>" method="post">
                <!--ACTION SPOOFING-->
                <input type="hidden" name="action" value="create"/>

                <label class="label">
                    <span class="legend">*Pergunta:</span>
                    <input type="text" name="question" placeholder="Pergunta frequente" required/>
                </label>

                <label class="label">
                    <span class="legend">*Resposta:</span>
                    <textarea name="response" rows="3" placeholder="Resolver a pergunta" required></textarea>
                </label>

                <label class="label">
                    <span class="legend">*Ordem:</span>
                    <input type="number" name="order_by" value="1" required/>
                </label>

                <div class="al-right">
                    <button class="btn btn-green icon-check-square-o">Cadastrar</button>
                </div>
            </form>
        </div>
    <?php else: ?>
        <header class="dash_content_app_header">
            <h2 class="icon-pencil-square-o">Editar Pergunta</h2>
            <a href="<?= url("/admin/faq/question/{$channel->id}"); ?>"
               class="icon-plus-circle btn btn-green">Nova Pergunta</a>
        </header>

        <div class="dash_content_app_box">
            <form class="app_form" action="<?= url("/admin/faq/question/{$channel->id}/{$question->id}"); ?>"
                  method="post">
                <!--ACTION SPOOFING-->
                <input type="hidden" name="action" value="update"/>

                <label class="label">
                    <span class="legend">*Pergunta:</span>
                    <input type="text" name="question" value="<?= $question->question; ?>"
                           placeholder="Pergunta frequente" required/>
                </label>

                <label class="label">
                    <span class="legend">*Resposta:</span>
                    <textarea name="response" rows="3" placeholder="Resolver a pergunta"
                              required><?= $question->response; ?></textarea>
                </label>

                <label class="label">
                    <span class="legend">*Ordem:</span>
                    <input type="number" name="order_by" value="<?= $question->order_by; ?>" required/>
                </label>

                <div class="app_form_footer">
                    <button class="btn btn-blue icon-check-square-o">Atualizar</button>
                    <a href="#" class="remove_link icon-error"
                       data-post="<?= url("/admin/faq/question/{$channel->id}/{$question->id}"); ?>"
                       data-action="delete"
                       data-confirm="Tem certeza que deseja excluir a perguntas e a respostas?"
                       data-question_id="<?= $question->id; ?>">Excluir Pergunta</a>
                </div>
            </form>
        </div>
    <?php endif; ?>
</section>
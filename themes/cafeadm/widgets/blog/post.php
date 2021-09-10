<?php $v->layout("_admin"); ?>
<?php $v->insert("widgets/blog/sidebar.php"); ?>

<div class="mce_upload" style="z-index: 997">
    <div class="mce_upload_box">
        <form class="app_form" action="<?= url("/admin/blog/post"); ?>" method="post" enctype="multipart/form-data">
            <input type="hidden" name="upload" value="true"/>
            <label>
                <label class="legend">Selecione uma imagem JPG ou PNG:</label>
                <input accept="image/*" type="file" name="image" required/>
            </label>
            <button class="btn btn-blue icon-upload">Enviar Imagem</button>
        </form>
    </div>
</div>

<section class="dash_content_app">
    <?php if (!$post): ?>
        <header class="dash_content_app_header">
            <h2 class="icon-plus-circle">Novo Artigo</h2>
        </header>

        <div class="dash_content_app_box">
            <form class="app_form" action="<?= url("/admin/blog/post"); ?>" method="post">
                <!-- ACTION SPOOFING-->
                <input type="hidden" name="action" value="create"/>

                <label class="label">
                    <span class="legend">*Título:</span>
                    <input type="text" name="title" placeholder="A manchete do seu artigo" required/>
                </label>

                <label class="label">
                    <span class="legend">*Subtítulo:</span>
                    <textarea name="subtitle" placeholder="O texto de apoio da manchete" required></textarea>
                </label>

                <label class="label">
                    <span class="legend">Capa: (1920x1080px)</span>
                    <input type="file" name="cover" placeholder="Uma imagem de capa"/>
                </label>

                <label class="label">
                    <span class="legend">Vídeo:</span>
                    <input type="text" name="video" placeholder="O ID de um vídeo do YouTube"/>
                </label>

                <label class="label">
                    <span class="legend">*Conteúdo:</span>
                    <textarea class="mce" name="content"></textarea>
                </label>

                <div class="label_g2">
                    <label class="label">
                        <span class="legend">*Categoria:</span>
                        <select name="category" required>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category->id; ?>"><?= $category->title; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <label class="label">
                        <span class="legend">*Autor:</span>
                        <select name="author" required>
                            <?php foreach ($authors as $author): ?>
                                <option value="<?= $author->id; ?>"><?= $author->fullName(); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                </div>

                <div class="label_g2">
                    <label class="label">
                        <span class="legend">*Status:</span>
                        <select name="status" required>
                            <option value="post">Publicar</option>
                            <option value="draft">Rascunho</option>
                            <option value="trash">Lixo</option>
                        </select>
                    </label>

                    <label class="label">
                        <span class="legend">Data de publicação:</span>
                        <input class="mask-datetime" type="text" name="post_at" value="<?= date("d/m/Y H:i"); ?>"
                               required/>
                    </label>
                </div>

                <div class="al-right">
                    <button class="btn btn-green icon-check-square-o">Publicar</button>
                </div>
            </form>
        </div>
    <?php else: ?>
        <header class="dash_content_app_header">
            <h2 class="icon-pencil-square-o">Editar post #<?= $post->id; ?></h2>
            <a class="icon-link btn btn-green" href="<?= url("/blog/{$post->uri}"); ?>" target="_blank" title="">Ver no
                site</a>
        </header>

        <div class="dash_content_app_box">
            <form class="app_form" action="<?= url("/admin/blog/post/{$post->id}"); ?>" method="post">
                <!-- ACTION SPOOFING-->
                <input type="hidden" name="action" value="update"/>

                <label class="label">
                    <span class="legend">*Título:</span>
                    <input type="text" name="title" value="<?= $post->title; ?>" placeholder="A manchete do seu artigo"
                           required/>
                </label>

                <label class="label">
                    <span class="legend">*Subtítulo:</span>
                    <textarea name="subtitle" placeholder="O texto de apoio da manchete"
                              required><?= $post->subtitle; ?></textarea>
                </label>

                <label class="label">
                    <span class="legend">Capa: (1920x1080px)</span>
                    <input type="file" name="cover" placeholder="Uma imagem de capa"/>
                </label>

                <label class="label">
                    <span class="legend">Vídeo:</span>
                    <input type="text" name="video" value="<?= $post->video; ?>"
                           placeholder="O ID de um vídeo do YouTube"/>
                </label>

                <label class="label">
                    <span class="legend">*Conteúdo:</span>
                    <textarea class="mce" name="content"><?= $post->content; ?></textarea>
                </label>

                <div class="label_g2">
                    <label class="label">
                        <span class="legend">*Categoria:</span>
                        <select name="category" required>
                            <?php foreach ($categories as $category):
                                $categoryId = $post->category;
                                $select = function ($value) use ($categoryId) {
                                    return ($categoryId == $value ? "selected" : "");
                                };
                                ?>
                                <option <?= $select($category->id); ?>
                                        value="<?= $category->id; ?>"><?= $category->title; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <label class="label">
                        <span class="legend">*Autor:</span>
                        <select name="author" required>
                            <?php foreach ($authors as $author):
                                $authorId = $post->author;
                                $select = function ($value) use ($authorId) {
                                    return ($authorId == $value ? "selected" : "");
                                };
                                ?>
                                <option <?= $select($author->id); ?>
                                        value="<?= $author->id; ?>"><?= $author->fullName(); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                </div>

                <div class="label_g2">
                    <label class="label">
                        <span class="legend">*Status:</span>
                        <select name="status" required>
                            <?php
                            $status = $post->status;
                            $select = function ($value) use ($status) {
                                return ($status == $value ? "selected" : "");
                            };
                            ?>
                            <option <?= $select("post"); ?> value="post">Publicar</option>
                            <option <?= $select("draft"); ?> value="draft">Rascunho</option>
                            <option <?= $select("trash"); ?> value="trash">Lixo</option>
                        </select>
                    </label>

                    <label class="label">
                        <span class="legend">Data de publicação:</span>
                        <input class="mask-datetime" type="text" name="post_at"
                               value="<?= date_fmt($post->post_at, "d/m/Y H:i"); ?>" required/>
                    </label>
                </div>

                <div class="al-right">
                    <button class="btn btn-blue icon-pencil-square-o">Atualizar</button>
                </div>
            </form>
        </div>
    <?php endif; ?>
</section>
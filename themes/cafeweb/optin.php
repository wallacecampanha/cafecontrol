<?php $v->layout("_theme"); ?>

<article class="optin_page">
    <div class="container content">
        <div class="optin_page_content">
            <img alt="<?= $data->title; ?>" title="<?= $data->title; ?>" src="<?= $data->image; ?>"/>
            <h1><?= $data->title; ?></h1>
            <p><?= $data->desc; ?></p>
            <?php if (!empty($data->link)): ?>
                <a class="optin_page_btn gradient gradient-green gradient-hover radius"
                   href="<?= $data->link; ?>" title="<?= $data->linkTitle; ?>"><?= $data->linkTitle; ?></a>
            <?php endif; ?>
        </div>
    </div>
</article>

<?php if (!empty($track)): ?>
    <?php $v->start("scripts"); ?>
    <script>
        fbq('track', '<?= $track->fb;?>');
        gtag('event', 'conversion', {'send_to': '<?= $track->aw;?>'});
    </script>
    <?php $v->end(); ?>
<?php endif; ?>

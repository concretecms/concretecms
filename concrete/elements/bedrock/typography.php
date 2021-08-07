<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>

<h1><?=t('Typography')?></h1>

<section class="mt-5">

<h3><?=t('Headings')?></h3>

<table class="table">
<tr>
    <th class="col-md-6"><?=t('Heading')?></th>
    <th class="col-md-6"><?=t('Example')?></th>
</tr>
<tbody>
<?php for ($i = 1; $i <= 6; $i++ ) { ?>
    <tr>
        <td class="align-middle text-center"><code><?=h(sprintf('<h%s></h%s>', $i, $i))?></code></td>
        <td class="align-middle"><h<?=$i?> class="mb-0">h<?=$i?>. <?=t('Heading')?></h<?=$i?>></td>
    </tr>
<?php } ?>
</tbody>
</table>

</section>

<section class="mt-5">

<h3><?=t('Display Headings')?></h3>

<table class="table">
    <tr>
        <th class="col-md-6"><?=t('Heading')?></th>
        <th class="col-md-6"><?=t('Example')?></th>
    </tr>
    <tbody>
    <?php for ($i = 1; $i <= 6; $i++ ) { ?>
        <tr>
            <td class="align-middle text-center"><code><?=h(sprintf('<div class="display-' . $i . '"></div>', $i, $i))?></code></td>
            <td class="align-middle"><div class="display-<?=$i?> mb-0">d<?=$i?> Heading</div></td>
        </tr>
    <?php } ?>
    </tbody>
</table>

</section>

<div class="row">
    <div class="col-md-6">



        <section class="mt-5">

            <h3><?=t('Paragraph & Inline Styles')?></h3>

            <p class="lead"><?=t('This is a lead paragraph. It stands out from regular paragraphs')?></p>
            <p><?=t('You can use the mark tag to <mark>highlight</mark> text.')?></p>
            <p><?=t('<del>This line of text is meant to be treated as deleted text.</del>')?></p>
            <p><?=t('<s>This line of text is meant to be treated as no longer accurate.</s>')?></p>
            <p><?=t('<ins>This line of text is meant to be treated as an addition to the document.</ins>')?></p>
            <p><?=t('<u>This line of text will render as underlined.</u>')?></p>
            <p><?=t('<small>This line of text is meant to be treated as fine print.')?></small></p>
            <p><?=t('<strong>This line rendered as bold text.</strong>')?></p>
            <p><?=t('<em>This line rendered as italicized text.</em>')?></p>

        </section>

    </div>

    <div class="col-md-6">

        <section class="mt-5">

            <h3><?=t('Blockquotes')?></h3>

            <blockquote class="blockquote">
                <p><?=t('This is an example quotation, for everyone to see.')?></p>
            </blockquote>

            <div class="pt-3 pb-3"></div>

            <figure>
                <blockquote class="blockquote">
                    <p><?=t('I really hope you enjoy Concrete CMS.')?></p>
                </blockquote>

                <figcaption class="blockquote-footer">
                    Andrew Embler <cite title="Source Title"><?=t('Concrete Core Team Leader')?></cite>
                </figcaption>
            </figure>


        </section>


        <section class="mt-5">

            <h3><?=t('Lists')?></h3>

            <h4><?=t('Styled Unordered')?></h4>

            <ul>
                <?php for ($i = 1; $i < 6; $i++) { ?>
                    <li><?=t('List Item')?> <?=$i?></li>

                <?php } ?>
            </ul>

            <h4><?=t('Unstyled')?></h4>

            <ul class="list-unstyled">
                <?php for ($i = 1; $i < 6; $i++) { ?>
                    <li><?=t('List Item')?> <?=$i?></li>

                <?php } ?>
            </ul>


        </section>
    </div>
</div>





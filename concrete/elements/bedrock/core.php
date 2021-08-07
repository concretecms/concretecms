<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>

<h1><?=t('Core Components')?></h1>

<p class="lead"><?=t('These HTML structures are used throughout Concrete blocks and are assumed to be handled by your theme.')?></p>

<section class="mt-5">

    <h3><?=t('Accordion')?></h3>

    <div class="accordion" id="accordionExample">
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingOne">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                    <?=t('Accordion 1')?>
                </button>
            </h2>
            <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                <div class="accordion-body">
                    Donec pulvinar metus at erat pulvinar, dignissim ornare nunc mollis. In lorem ex, finibus sit amet libero quis, ullamcorper ultricies felis. Integer id sapien sed augue aliquam semper. Vestibulum vestibulum purus sed turpis viverra aliquet. Cras at urna sodales, dignissim diam vel, rutrum odio. Fusce dui quam, egestas in dolor eu, pretium blandit orci. Nulla tristique a tortor et sagittis. Donec dignissim iaculis libero.
                </div>
            </div>
        </div>
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingTwo">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                    <?=t('Accordion 2')?>
                </button>
            </h2>
            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                <div class="accordion-body">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum efficitur quam vitae odio euismod, porttitor lobortis arcu semper. Suspendisse rutrum eros id bibendum varius. Nunc vel velit ex. Nam non ligula in ex feugiat facilisis at vitae eros. Vestibulum aliquam, ex vel accumsan mattis, lorem mauris posuere elit, id elementum nisl mauris vitae arcu. Mauris tincidunt ligula a augue gravida, at cursus est interdum. Aenean tortor massa, posuere et venenatis id, aliquam id tellus. Suspendisse eleifend nunc sit amet erat euismod, in congue lorem scelerisque. Sed nec velit molestie, maximus tellus vitae, maximus enim. Vestibulum imperdiet dolor non nisi luctus imperdiet. Aliquam gravida elit non dui euismod ullamcorper non id elit. Curabitur ultricies erat ac odio imperdiet auctor. Etiam euismod sodales magna vel rutrum. Donec fringilla a est eget volutpat. Cras non scelerisque nisi, eget luctus augue. Vivamus viverra nunc nec elementum dictum.
                </div>
            </div>
        </div>
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingThree">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                    <?=t('Accordion 3')?>
                </button>
            </h2>
            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                <div class="accordion-body">
                    In pharetra vel odio tristique gravida. Cras non mauris non turpis egestas ullamcorper eget varius lorem. Aenean eget velit nec lectus aliquet varius eget ut felis. Aenean consectetur ipsum id dolor facilisis, ut cursus leo dignissim. Etiam vitae efficitur dui, a placerat ipsum. Vivamus ornare sed neque non sodales. Suspendisse ac nisl eu mauris sagittis accumsan vitae eget enim.
                </div>
            </div>
        </div>
    </div>


</section>

<section class="mt-5">

    <h3><?=t('Alerts')?></h3>

    <div class="row">
        <?php foreach ($colors as $color) {

            $key = $color[0];
            $label = $color[1];
            ?>

            <div class="col-md-6">
                <div class="alert alert-<?=$key?>"><?=t('This is a %s alert.', $label)?></div>
            </div>

        <?php } ?>
    </div>
</section>

<section class="mt-5">

    <h3><?=t('Badges')?></h3>

    <div class="row">
        <div class="col-md-6">
        <?php foreach ($colors as $color) {

            $key = $color[0];
            $label = $color[1];
            $textColor = isset($color[2]) ? $color[2] : 'text-light';
            ?>
                <span class="badge bg-<?=$key?> <?=$textColor?>"><?=$label?></span>
        <?php } ?>
        </div>

        <div class="col-md-6">
            <?php foreach ($colors as $color) {

                $key = $color[0];
                $label = $color[1];
                $textColor = isset($color[2]) ? $color[2] : 'text-light';
                ?>
                <span class="badge rounded-pill bg-<?=$key?> <?=$textColor?>"><?=$label?></span>
            <?php } ?>
        </div>
    </div>
</section>


<section class="mt-5">
    <h3><?=t('Breadcrumb')?></h3>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><?=t('Home')?></a></li>
            <li class="breadcrumb-item"><a href="#"><?=t('Company')?></a></li>
            <li class="breadcrumb-item active" aria-current="page"><?=t('Careers')?></li>
        </ol>
    </nav>
</section>

<section class="mt-5">

    <h3><?=t('Buttons')?></h3>

    <section class="mt-3">
        <h4><?=t('Regular')?></h4>
        <div>
        <?php foreach ($colors as $color) {

            $key = $color[0];
            $label = $color[1];
            $textColor = isset($color[2]) ? $color[2] : 'text-light';
            ?>
            <button type="button" class="btn btn-<?=$key?> <?=$textColor?>"><?=$label?></button>
        <?php } ?>
        </div>
    </section>

    <section class="mt-3">
        <h4><?=t('Outline')?></h4>
        <div>
            <?php foreach ($colors as $color) {

                $key = $color[0];
                $label = $color[1];
                ?>
                <button type="button" class="btn btn-outline-<?=$key?>"><?=$label?></button>
            <?php } ?>
        </div>
    </section>

    <div class="row">
        <div class="col-md-4">
            <section class="mt-5">
                <h4><?=t('Large')?></h4>
                <div>
                    <button type="button" class="btn btn-lg btn-primary"><?=t('Large Button')?></button>
                    <button type="button" class="btn btn-lg btn-secondary"><?=t('Large Button')?></button>
                </div>
            </section>
        </div>

        <div class="col-md-4">
            <section class="mt-5">
                <h4><?=t('Small')?></h4>
                <div>
                    <button type="button" class="btn btn-sm btn-primary"><?=t('Small Button')?></button>
                    <button type="button" class="btn btn-sm btn-secondary"><?=t('Small Button')?></button>
                </div>
            </section>
        </div>

        <div class="col-md-4">
            <section class="mt-5">
                <h4><?=t('Disabled')?></h4>
                <div>
                    <button type="button" class="btn btn-primary" disabled><?=t('Button')?></button>
                    <button type="button" class="btn btn-secondary" disabled><?=t('Button')?></button>
                </div>
            </section>
        </div>

    </div>

</section>

<section class="mt-5">
    <h3><?=t('Forms')?></h3>

    <?php
    $controller = app(\Concrete\Attribute\Text\Controller::class);
    $type = new \Concrete\Core\Entity\Attribute\Type();
    $type->setAttributeTypeHandle('text');
    $key = new \Concrete\Core\Entity\Attribute\Key\Key();
    $key->setAttributeKeyName('hi');
    $key->setAttributeType($type);

    $form = new \Concrete\Core\Entity\Express\Form();
    $set = new \Concrete\Core\Entity\Express\FieldSet();
    $control = new Concrete\Core\Entity\Express\Control\AttributeKeyControl();
    $control->setAttributeKey($key);

    $set->getControls()->add($control);
    $form->getFieldSets()->add($set);

    $context = new \Concrete\Core\Express\Form\Context\FrontendFormContext();
    $renderer = new \Concrete\Core\Express\Form\Renderer($context, $form);
    $renderer->render();
    ?>
</section>


<section class="mt-5">
    <h3><?=t('Pagination')?></h3>

    <?=$paginationCallable()?>
</section>


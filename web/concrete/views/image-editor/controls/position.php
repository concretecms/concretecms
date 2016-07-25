<div class="row">
    <div class="col-sm-12 row">
        <div class="form-group">
            <label for="scale" class="control-label"><?= t('Scale') ?></label>
            <div class="scale-container">
                <div class="scale-slider col-sm-10"></div>
                <div class="col-sm-2">
                    <span class="scale-percent">100%</span>
                </div>
            </div>
        </div>
    </div>
</div>
<div>
    <div class="col-sm-10 row">
        <div class="form-group">
            <label for="width" class="control-label"><?= t('Width') ?></label>

            <div class="input-group">
                <input class="form-control" name="width" type="number">

                <div class="input-group-addon">px</div>
            </div>
        </div>

        <div class="form-group">
            <label for="height" class="control-label"><?= t('Height') ?></label>

            <div class="input-group">
                <input class="form-control" name="height" type="number">

                <div class="input-group-addon">px</div>
            </div>
        </div>
    </div>
    <div class="col-sm-1 col-sm-offset-1 lock-button">
        <button class="btn btn-default lock active">
            <i class="fa fa-lock"></i>
        </button>
    </div>
</div>
<div class="form-group">
    <div class="btn-group">
        <button class="rot btn btn-default">
            <i class="fa fa-rotate-right"></i>
        </button>
        <button class="vflip btn btn-default">
            <i class="fa fa-arrows-v"></i>
        </button>
        <button class="hflip btn btn-default">
            <i class="fa fa-arrows-h"></i>
        </button>
    </div>
    <button class="reset btn btn-default"><?= t('Fit Image'); ?></button>
</div>

<div class="form-group crop">
    <div class="row">
        <div class="col-sm-12">
            <button class="btn btn-block btn-success begincrop"><i class="fa fa-crop"></i> <?= t('Crop Image') ?></button>
        </div>
    </div>
    <br>
    <div class="row crop-area" style="display:none">
        <div>
            <div class="col-sm-10 row">
                <div class="form-group">
                    <label for="cropwidth" class="control-label"><?= t('Width') ?></label>

                    <div class="input-group">
                        <input class="form-control" name="cropwidth" type="number">

                        <div class="input-group-addon">px</div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="cropheight" class="control-label"><?= t('Height') ?></label>

                    <div class="input-group">
                        <input class="form-control" name="cropheight" type="number">

                        <div class="input-group-addon">px</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-1 col-sm-offset-1 lock-button">
                <button class="btn btn-default croplock active">
                    <i class="fa fa-lock"></i>
                </button>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <button class="btn cancel"><?=t('Cancel')?></button>
            </div>
            <div class="col-sm-6">
                <button class="btn docrop btn-primary pull-right"><?=t('Finalize Crop')?></button>
            </div>
        </div>
    </div>
</div>

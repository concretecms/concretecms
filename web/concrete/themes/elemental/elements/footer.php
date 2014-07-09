<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<footer id="footer-theme">
    <section>
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <?
                $a = new GlobalArea('Footer Site Title');
                $a->display();
                ?>
            </div>
            <div class="col-md-9"></div>
        </div>
    </div>
    </section>
    <section>
    <div class="container">
        <div class="row">
            <div class="col-md-6">
            <?
            $a = new GlobalArea('Footer Legal');
            $a->display();
            ?>
            </div>
            <div class="col-md-3">
                <?
                $a = new GlobalArea('Footer Navigation');
                $a->display();
                ?>
            </div>
            <div class="col-md-3">
                <?
                $a = new GlobalArea('Footer Contact');
                $a->display();
                ?>
            </div>
        </div>
    </div>
    </section>
</footer>

<footer id="concrete5-brand">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <span><?=t('Built with <a href="http://www.concrete5.org" class="concrete5">concrete5</a> CMS.')?></span>
                <span class="pull-right">
                    <a href="<?=URL::to('/login')?>">Login</a>
                </span>
            </div>
        </div>
    </div>
</footer>


<? $this->inc('elements/footer_bottom.php');?>
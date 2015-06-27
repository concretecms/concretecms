<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<!DOCTYPE html>
<html lang="<?=Localization::activeLanguage()?>">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" type="text/css" href="<?=$view->getThemePath()?>/css/bootstrap-modified.css">
    <?=$html->css($view->getStylesheet('main.less'))?>
    <?php Loader::element('header_required', array('pageTitle' => $pageTitle));?>
    <script type="text/javascript" src="<?=$view->getThemePath()?>/js/parallax.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>
        if (navigator.userAgent.match(/IEMobile\/10\.0/)) {
            var msViewportStyle = document.createElement('style')
            msViewportStyle.appendChild(
                document.createTextNode(
                    '@-ms-viewport{width:auto!important}'
                )
            )
            document.querySelector('head').appendChild(msViewportStyle)
        }

        $(function() {
            $('.parallax-slow').each(function() {
                var $element = $(this),
                    innerContent = $element.html(),
                    background = $(this).css('background-image').slice(4, -1).replace(/"/g, ""),
                    $inner = $('<div />', {'class': 'parallax-stripe-inner'});

                $element.html('').addClass('parallax-stripe')
                    .attr('data-background-image', background);
                $element.css('background-image', 'none');
                $inner.html(innerContent).appendTo($element);

                $('div.parallax-stripe').each(function () {
                    $(this).parallaxize({
                        variation: 240
                    });
                });
            });
        });
    </script>
</head>
<body>

<div class="<?=$c->getPageWrapperClass()?>">

<?php
    defined('C5_EXECUTE') or die('Access Denied.');
    $a = $b->getBlockAreaObject();

    $rootContainer = $formatter->getLayoutContainerHtmlObject();
    if (!empty($rootContainer)) {
        $container = $rootContainer;
        while ($container->hasChildren()) {
            $container = $container->getChildren()[0];
        }
    }

    foreach ($columns as $column) {
        $html = $column->getColumnHtmlObject();
        if (!empty($container)) {
            $container->appendChild($html);
        } else {
            echo $html;
        }
    }

    echo $rootContainer;

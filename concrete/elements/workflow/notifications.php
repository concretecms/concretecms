<?php defined('C5_EXECUTE') or die("Access Denied.");

if (is_array($workflowList) && !empty($workflowList)) {
    $app = Concrete\Core\Support\Facade\Facade::getFacadeApplication();
    $cih = $app->make('helper/concrete/ui');

    foreach ($workflowList as $i => $wl) {
        $wr = $wl->getWorkflowRequestObject();
        $wf = $wl->getWorkflowObject();
        $form = '<form data-form="workflow" method="post" action="' . $wl->getWorkflowProgressFormAction() . '">';
        $text = $wf->getWorkflowProgressCurrentDescription($wl);

        $actions = $wl->getWorkflowProgressActions();
        $buttons = [];

        if (!empty($actions)) {
            foreach ($actions as $act) {
                $parameters = 'class="btn btn-xs ' . $act->getWorkflowProgressActionStyleClass() . '" ';
                if (!empty($act->getWorkflowProgressActionExtraButtonParameters())) {
                    foreach ($act->getWorkflowProgressActionExtraButtonParameters() as $key => $value) {
                        $parameters .= $key . '="' . $value . '" ';
                    }
                }

                $inner = $act->getWorkflowProgressActionStyleInnerButtonLeftHTML() . ' ' .
                $act->getWorkflowProgressActionLabel() . ' ' .
                $act->getWorkflowProgressActionStyleInnerButtonRightHTML();

                if ($act->getWorkflowProgressActionURL() != '') {
                    $button = '<a href="' . $act->getWorkflowProgressActionURL() . '" ' . $parameters . '>' . $inner . '</a>';
                } else {
                    $button = '<button type="submit" name="action_' . $act->getWorkflowProgressActionTask() . '" ' . $parameters . '>' . $inner . '</button>';
                }

                $buttons[] = $button;
            }
        }

        if ($displayInline) {
            echo $form;
            echo implode("\n", $buttons);
            echo '</form>';
        } else {
            echo $cih->notify(array(
                'text' => $text,
                'type' => 'info',
                'form' => $form,
                'icon' => 'fa fa-info-circle',
                'buttons' => $buttons
            ));
        }
    }
}

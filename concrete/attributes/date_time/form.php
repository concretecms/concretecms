<?php
defined('C5_EXECUTE') or die("Access Denied.");
switch ($displayMode) {
                        case 'text':
                            $format = $date->getPHPDateTimePattern();
                            if ($value === null) {
                                $placeholder = $date->formatCustom($format, 'now');
                            } else {
                                $value = $date->formatCustom($format, $value);
                                $placeholder = $value;
                            }
                            $form = $this->app->make('helper/form');
                            echo $form->text($this->field('value'), $value, ['placeholder' => $placeholder]);
                            break;
                        case 'date':
                            $this->requireAsset('jquery/ui');
                            echo $form_date_time->date($this->field('value'), $value);
                            break;
                        default:
                            $this->requireAsset('jquery/ui');
                            echo $form_date_time->datetime($this->field('value'), $value, false, true, null, $timeResolution);
                            break;
                    }
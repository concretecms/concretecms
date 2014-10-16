<?php
namespace Concrete\Core\Form\Service\Widget;

use Concrete\Core\Http\ResponseAssetGroup;
use Loader;
use Page;

class PageSelector
{

    /**
     * Creates form fields and JavaScript page chooser for choosing a page. For use with inclusion in blocks.
     * <code>
     *     $dh->selectPage('pageID', '1'); // prints out the home page and makes it selectable.
     * </code>
     *
     * @param int $cID
     */

    public function selectPage($fieldName, $cID = false)
    {
        $r = ResponseAssetGroup::get();
        $r->requireAsset('core/sitemap');
        $selectedCID = 0;
        if (isset($_REQUEST[$fieldName])) {
            $selectedCID = Loader::helper('security')->sanitizeInt($_REQUEST[$fieldName]);
        } else {
            if ($cID > 0) {
                $selectedCID = $cID;
            }
        }

        // prevent that fieldnames such as 'field[2]' throw errors
        $identifier = new \Concrete\Core\Utility\Service\Identifier();
        $safeFieldName = $identifier->getString(32);

        $html = '';
        $clearStyle = 'display: none';
        $html .= '<div class="ccm-summary-selected-item" data-page-selector="' . $safeFieldName . '"><div class="ccm-summary-selected-item-inner"><strong class="ccm-summary-selected-item-label">';
        if ($selectedCID > 0) {
            $oc = Page::getByID($selectedCID);
            $html .= $oc->getCollectionName();
            $clearStyle = '';
        }
        $html .= '</strong></div>';
        $html .= '<a class="ccm-sitemap-select-page" data-page-selector-launch="' . $safeFieldName . '" dialog-width="90%" dialog-height="70%" dialog-append-buttons="true" dialog-modal="false" dialog-title="' . t(
                'Choose Page') . '" href="' . REL_DIR_FILES_TOOLS_REQUIRED . '/sitemap_search_selector?cID=' . $selectedCID . '" dialog-on-close="Concrete.event.fire(\'fileselectorclose\', \'{$fieldName}\');">' . t(
                'Select Page') . '</a>';
        $html .= '&nbsp;<a href="javascript:void(0)" dialog-sender="' . $safeFieldName . '" data-page-selector-clear="' . $safeFieldName . '" class="ccm-sitemap-clear-selected-page" style="float: right; margin-top: -8px;' . $clearStyle . '"><img src="' . ASSETS_URL_IMAGES . '/icons/remove.png" style="vertical-align: middle; margin-left: 3px" /></a>';
        $html .= '<input type="hidden" data-page-selector="cID" name="' . $fieldName . '" value="' . $selectedCID . '"/>'; // << don't use $safeFieldName here, but use the original one
        $html .= '</div>';
        $html .= "<script type=\"text/javascript\">
                   $(function() {
                        var ccmActivePageField;
                        var launcher = $('a[data-page-selector-launch=\"{$safeFieldName}\"]'), name = '{$safeFieldName}', openEvent, openEvent2;
                        var container = $('div[data-page-selector=\"' + name + '\"]');
                        launcher.dialog();
                        ConcreteEvent.bind('fileselectorclose', function(field_name) {
                            ConcreteEvent.unbind('ConcreteSitemap.' + name);
                            ConcreteEvent.unbind('SitemapSelectPage.' + name);
                            ConcreteEvent.unbind('ConcreteSitemapPageSearch.' + name);
                        });
                        launcher.on('click', function () {
                            var selector = $(this),
                                handle_select = function(e, data) {
                                    ConcreteEvent.unbind(e);
                                    var handle = selector.attr('data-page-selector-launch');
                                    container.find('.ccm-summary-selected-item-label').html(data.title);
                                    container.find('.ccm-sitemap-clear-selected-page').show();
                                    container.find('input[data-page-selector=cID]').val(data.cID);
                                    $.fn.dialog.closeTop();
                                };

                            ConcreteEvent.bind('ConcreteSitemap.' + name, function (event, sitemap) {
                                ConcreteEvent.subscribe('SitemapSelectPage.' + name, function (e, data) {
                                    if (data.instance === sitemap) {
                                        handle_select(e, data);
                                    }
                                });
                            });

                            ConcreteEvent.bind('ConcreteSitemapPageSearch.' + name, function (event, search) {

                                ConcreteEvent.subscribe('SitemapSelectPage.' + name, function (e, data) {
                                    if (data.instance === search) {
                                        handle_select(e, data);
                                    }
                                });
                            });
                        });

                        $('a[data-page-selector-clear={$safeFieldName}]').click(function () {
                            var container = $('div[data-page-selector={$safeFieldName}]');
                            container.find('.ccm-summary-selected-item-label').html('');
                            container.find('.ccm-sitemap-clear-selected-page').hide();
                            container.find('input[data-page-selector=cID]').val('');
                        });
                  });
                  </script>";
        return $html;
    }

    /* Embed a sitemap in javascript dialog.  Supports the following args:
     *  'node_action'   - path to script containing code to be execute when user clicks on a node in the sitemap
     *  'dialog_title'  - dialog title
     *  'dialog_height' - dialog height (default: 350px)
     *  'dialog_width'  - dialog width (default: 350px)
     *  'target_id'     - id of the (hidden) field on the parent page that is to receive the CID of the chosen page
     *                    (do not include the '#')
     *  (any other arguments the dashboard/sitemap element supports)
     */
    public function sitemap($args)
    {
        /*
        if (!isset($args['select_mode'])) {
            $args['select_mode'] = 'move_copy_delete';
        }
        if (empty($args['node_action'])) {
            $args['node_action'] = '<none>';
        }
        if (empty($args['display_mode'])) {
            $args['display_mode'] = 'full';
        }
        if (empty($args['instance_id'])) {
            $args['instance_id'] = time();
        }
        Loader::element('dashboard/sitemap', $args);
        */
    }

    public function quickSelect($key, $cID = false, $args = array())
    {
        $selectedCID = 0;
        if (isset($_REQUEST[$key])) {
            $selectedCID = $_REQUEST[$key];
        } else {
            if ($cID > 0) {
                $selectedCID = $cID;
            }
        }

        $cName = '';
        if ($selectedCID > 0) {
            $oc = Page::getByID($selectedCID);
            $cp = new Permissions($oc);
            if ($cp->canViewPage()) {
                $cName = $oc->getCollectionName();
            }
        }

        $form = Loader::helper('form');
        $valt = Loader::helper('validation/token');
        $token = $valt->generate('quick_page_select_' . $key);
        $html .= "
		<script type=\"text/javascript\">
		$(function () {
			$('#ccm-quick-page-selector-label-" . $key . "').autocomplete({
				select: function(e, ui) {
					$('#ccm-quick-page-selector-label-" . $key . "').val(ui.item.label);
					$('#ccm-quick-page-selector-value-" . $key . "').val(ui.item.value);
					return false;
				},
				open: function(e, ui) {
					//$('#ccm-quick-page-selector-label-" . $key . "').val('');
					$('#ccm-quick-page-selector-value-" . $key . "').val('');
				},
				focus: function(e, ui) {
					$('#ccm-quick-page-selector-label-" . $key . "').val(ui.item.label);
					return false;
				},
				source: '" . REL_DIR_FILES_TOOLS_REQUIRED . "/pages/autocomplete?key=" . $key . "&token=" . $token . "'
			});
			$('#ccm-quick-page-selector-label-" . $key . "').keydown(function(e) {
				if (e.keyCode == 13) {
					e.preventDefault();
				}
			}).change(function(e) {
				if ($('#ccm-quick-page-selector-label-" . $key . "').val() == '') {
					$('#ccm-quick-page-selector-value-" . $key . "').val('');
				}
			});
			$('#ccm-quick-page-selector-label-" . $key . "').autocomplete('widget').addClass('ccm-page-selector-autocomplete');
		} );
		</script>";
        $html .= '<input type="hidden" id="ccm-quick-page-selector-value-' . $key . '" name="' . $key . '" value="' . $selectedCID . '" /><span class="ccm-quick-page-selector">
		<input type="text" class="ccm-input-text" name="ccm-quick-page-selector-label-' . $key . '" id="ccm-quick-page-selector-label-' . $key . '" value="' . $cName . '" /></span>';
        return $html;
    }

}

<?php

use \Concrete\Core\Localization\Localization;

header('Content-type: text/javascript; charset=' . APP_CHARSET);

$jh = Core::make('helper/json');

$locale = Localization::activeLocale();

?>
jQuery.Redactor.opts.langs[<?=$jh->encode($locale)?>] = {
    html: <?=$jh->encode(t('HTML'))?>,
    video: <?=$jh->encode(t('Insert Video'))?>,
    image: <?=$jh->encode(t('Insert Image'))?>,
    table: <?=$jh->encode(t('Table'))?>,
    link: <?=$jh->encode(t('Link'))?>,
    link_insert: <?=$jh->encode(t('Insert link'))?>,
    link_edit: <?=$jh->encode(t('Edit link'))?>,
    unlink: <?=$jh->encode(t('Unlink'))?>,
    formatting: <?=$jh->encode(t('Formatting'))?>,
    paragraph: <?=$jh->encode(t('Normal text'))?>,
    quote: <?=$jh->encode(t('Quote'))?>,
    code: <?=$jh->encode(t('Code'))?>,
    header1: <?=$jh->encode(t('Header 1'))?>,
    header2: <?=$jh->encode(t('Header 2'))?>,
    header3: <?=$jh->encode(t('Header 3'))?>,
    header4: <?=$jh->encode(t('Header 4'))?>,
    header5: <?=$jh->encode(t('Header 5'))?>,
    /* concrete5 */
    header6: <?=$jh->encode(t('Header 6'))?>,
    customStyles: <?=$jh->encode(t('Custom Styles'))?>,
    /* end concrete5 */
    bold: <?=$jh->encode(t('Bold'))?>,
    italic: <?=$jh->encode(t('Italic'))?>,
    fontcolor: <?=$jh->encode(t('Font Color'))?>,
    backcolor: <?=$jh->encode(t('Back Color'))?>,
    unorderedlist: <?=$jh->encode(t('Unordered List'))?>,
    orderedlist: <?=$jh->encode(t('Ordered List'))?>,
    outdent: <?=$jh->encode(t('Outdent'))?>,
    indent: <?=$jh->encode(t('Indent'))?>,
    cancel: <?=$jh->encode(t('Cancel'))?>,
    insert: <?=$jh->encode(t('Insert'))?>,
    save: <?=$jh->encode(t('Save'))?>,
    _delete: <?=$jh->encode(t('Delete'))?>,
    insert_table: <?=$jh->encode(t('Insert Table'))?>,
    insert_row_above: <?=$jh->encode(t('Add Row Above'))?>,
    insert_row_below: <?=$jh->encode(t('Add Row Below'))?>,
    insert_column_left: <?=$jh->encode(t('Add Column Left'))?>,
    insert_column_right: <?=$jh->encode(t('Add Column Right'))?>,
    delete_column: <?=$jh->encode(t('Delete Column'))?>,
    delete_row: <?=$jh->encode(t('Delete Row'))?>,
    delete_table: <?=$jh->encode(t('Delete Table'))?>,
    rows: <?=$jh->encode(t('Rows'))?>,
    columns: <?=$jh->encode(t('Columns'))?>,
    add_head: <?=$jh->encode(t('Add Head'))?>,
    delete_head: <?=$jh->encode(t('Delete Head'))?>,
    title: <?=$jh->encode(t('Title'))?>,
    image_position: <?=$jh->encode(t('Position'))?>,
    none: <?=$jh->encode(t('None'))?>,
    left: <?=$jh->encode(t('Left'))?>,
    right: <?=$jh->encode(t('Right'))?>,
    center: <?=$jh->encode(t('Center'))?>,
    image_web_link: <?=$jh->encode(t('Image Web Link'))?>,
    text: <?=$jh->encode(t('Text'))?>,
    mailto: <?=$jh->encode(t('Email'))?>,
    web: <?=$jh->encode(t('URL'))?>,
    video_html_code: <?=$jh->encode(t('Video Embed Code'))?>,
    file: <?=$jh->encode(t('Insert File'))?>,
    upload: <?=$jh->encode(t('Upload'))?>,
    download: <?=$jh->encode(t('Download'))?>,
    choose: <?=$jh->encode(t('Choose'))?>,
    or_choose: <?=$jh->encode(t('Or choose'))?>,
    drop_file_here: <?=$jh->encode(t('Drop file here'))?>,
    align_left: <?=$jh->encode(t('Align text to the left'))?>,
    align_center: <?=$jh->encode(t('Center text'))?>,
    align_right: <?=$jh->encode(t('Align text to the right'))?>,
    align_justify: <?=$jh->encode(t('Justify text'))?>,
    horizontalrule: <?=$jh->encode(t('Insert Horizontal Rule'))?>,
    deleted: <?=$jh->encode(t('Deleted'))?>,
    anchor: <?=$jh->encode(t('Anchor'))?>,
    open_link: <?=$jh->encode(t('Open link'))?>,
    default_behavior: <?=$jh->encode(t('Default Behavior'))?>,
    in_lightbox: <?=$jh->encode(t('In a Lightbox'))?>,
    open_link_in_lightbox: <?=$jh->encode(t('Open Link in Lightbox'))?>,
    lightbox_link_type: <?=$jh->encode(t('Link Type'))?>,
    lightbox_link_type_iframe: <?=$jh->encode(t('Web Page'))?>,
    lightbox_link_type_iframe_options: <?=$jh->encode(t('Frame Options'))?>,
    lightbox_link_type_iframe_width: <?=$jh->encode(t('Width'))?>,
    lightbox_link_type_iframe_height: <?=$jh->encode(t('Height'))?>,
    lightbox_link_type_image: <?=$jh->encode(t('Image'))?>,
    link_new_tab: <?=$jh->encode(t('Open link in new tab'))?>,
    link_same_window: <?=$jh->encode(t('Open link in same window'))?>,
    underline: <?=$jh->encode(t('Underline'))?>,
    alignment: <?=$jh->encode(t('Alignment'))?>,
    filename: <?=$jh->encode(t('Name (optional)'))?>,
    edit: <?=$jh->encode(t('Edit'))?>
};

jQuery.Redactor.opts.lang = <?=$jh->encode($locale)?>;
jQuery.each(jQuery.Redactor.opts.langs.en, function(key, value) {
    if(!(key in jQuery.Redactor.opts.langs[<?=$jh->encode($locale)?>])) {
        jQuery.Redactor.opts.langs[<?=$jh->encode($locale)?>][key] = value;
    }
});

var ccmi18n_redactor = {
    remove_font: <?=$jh->encode(t('Remove font'))?>,
    change_font_family: <?=$jh->encode(t('Change font family'))?>,
    remove_font_size: <?=$jh->encode(t('Remove font size'))?>,
    change_font_size: <?=$jh->encode(t('Change font size'))?>,
    cancel: <?=$jh->encode(t('Cancel'))?>,
    save: <?=$jh->encode(t('Save'))?>,
    remove_style: <?=$jh->encode(t('Remove Style'))?>
 };
 
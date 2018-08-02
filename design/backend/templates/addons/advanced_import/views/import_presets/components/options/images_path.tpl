{capture name="images_path_note"}
    {__("text_file_editor_notice_full_link", ["[link]" => "<a class=\"advanced-import-file-editor-opener\" data-target-input-id=\"{$option_id}\">{__("file_editor")}</a>"])}
{/capture}

{$option.notes = $smarty.capture.images_path_note scope="root"}

<div class="input-prepend">
    <span class="add-on" id="advanced_import_images_path_prefix" data-companies-image-directories="{$option.companies_image_directories|to_json}">
        {$option.input_prefix}
    </span>

    <input id="{$option_id}"
        class="input-large prefixed"
        type="text"
        name="{$field_name_prefix}[{$option_id}]"
        value="{$option.display_value}"
    />
</div>

<div id="{$option_id}_dialog" class="hidden"></div>

<script type="text/javascript">
    (function(_, $) {
        _.tr({
            file_editor: '{__("file_editor")|escape:"javascript"}'
        });
    }(Tygh, Tygh.$));
</script>

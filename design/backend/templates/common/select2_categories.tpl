{script src="js/tygh/backend/select2_categories.js"}
<div class="object-categories-add cm-object-categories-add-container {$select2_wrapper_meta}">
    {$select_id=$select2_select_id|default:"categories_add"}
    {$category_ids = [$select2_main_category]|array_merge:$select2_category_ids|default:[]|array_unique}

    <input type="hidden" name="{$select2_name}" value="" />

    {if $select2_multiple}
        {$select2_name="`$select2_name`[]"}
    {/if}

    <select id="{$select_id}"
        class="cm-object-selector cm-object-categories-add {$select2_select_meta}"
        {if $select2_tabindex}
            tabindex="{$select2_tabindex}"
        {/if}
        {if $select2_multiple}
            multiple
        {/if}
        name="{$select2_name}"
        data-ca-enable-images="{$select2_enable_images|default:"true"}"
        data-ca-enable-search="{$select2_enable_search|default:"true"}"
        data-ca-load-via-ajax="{$select2_load_via_ajax|default:"true"}"
        data-ca-page-size="{$select2_page_size|default:10}"
        data-ca-data-url="{$select2_data_url|default:"categories.get_categories_list"|fn_url nofilter}"
        data-ca-placeholder="{$select2_placeholder|default:__("type_to_search")}"
        data-ca-allow-clear="{$select2_allow_clear|default:"false"}"
        data-ca-close-on-select="{$select2_close_on_select|default:"false"}"
        data-ca-ajax-delay="{$select2_ajax_delay|default:250}"
        data-ca-allow-sorting="{$select2_allow_sorting|default:"false"}"
        data-ca-escape-html="{$select2_escape_html|default:"false"}"
        data-ca-dropdown-css-class="{$select2_dropdown_css_class|default:"select2-dropdown-below-categories-add"}"
        data-ca-required="{$select2_required|default:"false"}"
        data-ca-select-width="{$select2_width|default:"100%"}"
        data-ca-repaint-dropdown-on-change="{$select2_repaint_dropdown_on_change|default:"true"}"
    >
        {if $category_ids}
            {foreach from=$category_ids|array_unique item="category_id"}
                <option value="{$category_id}" selected="selected"></option>
            {/foreach}
        {/if}
    </select>
</div>

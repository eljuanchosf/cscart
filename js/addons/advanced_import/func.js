(function (_, $) {

    var preset_id, object_type,
        company_selector, file_type_selector,
        file_selector, preset_name_selector,
        xml_target_node_wrapper,
        nesting_padding_size = 20;

    var methods = {
        init_preset_editor: function () {

            $.ceEvent('on', 'ce.fileuploader.display_filename', function (id, file_type, file) {
                if (file_type === 'server' || file_type === 'url') {
                    file = $('#file_' + id).val();
                    $.ceAdvancedImport('get_fields', file_type, file);
                    $('#advanced_import_save_and_import, li#fields').removeClass('hidden');
                } else if (file_type === 'local') {
                    $('#advanced_import_save_and_import, li#fields').addClass('hidden');
                }

                var file_extension = /\.([^.]+)?$/.exec(file);

                if (!file_extension || file_extension[1] !== 'xml') {
                    xml_target_node_wrapper.hide();
                } else {
                    xml_target_node_wrapper.show();
                }

                if (!preset_name_selector.val()) {
                    preset_name_selector.val(file)
                }
            });

            if (xml_target_node_wrapper.data('caDefaultHidden')) {
                xml_target_node_wrapper.hide();
            }
        },

        get_fields: function (file_type, file, options) {

            file_type = file_type || file_type_selector.val();
            file = file || file_selector.val();

            var company_id = company_selector.val();

            var data = {
                preset_id: preset_id,
                object_type: object_type,
                company_id: company_id
            };
            if (file_type) {
                data.file_type = file_type;
            }
            if (file) {
                data.file = file;
            }

            $.ceAjax('request', fn_url('import_presets.get_fields'), {
                result_ids: 'content_fields',
                caching: false,
                data: data
            });
        },

        init_related_object_selectors: function (selectors) {
            selectors.change(function () {
                var type_holder = $('#elm_field_related_object_type_' + $(this).data('caAdvancedImportFieldId'));
                var type = $('option:selected', $(this)).data('caAdvancedImportFieldRelatedObjectType');
                type_holder.val(type);
            });

            selectors.each(function () {
                $(this).trigger('change');
            });
        },

        show_fields_preview: function (opener) {
            var params = $.ceDialog('get_params', opener);
            $('#' + opener.data('caTargetId')).ceDialog('open', params);
            if (window.history.replaceState) {
                window.history.replaceState({}, '', _.current_url.replace(/&preview_preset_id=\d+/, ''));
            }
        },

        // FIXME: Dirty hack
        // Pop-up with the fields mapping is destroyed before a Comet request is sent,
        // so fields must be manually transfered to the parent form.
        set_fields_for_import: function (preset_id) {
            var form = $('[data-ca-advanced-import-element="management_form"]');

            form.append('<input type="hidden" name="preset_id" value="' + preset_id + '" />');
            form.append('<input type="hidden" name="dispatch[advanced_import.import]" value="OK" />');

            var fields = form.serializeArray();

            for (var i in fields) {
                var field = fields[i];
                if (/^fields\[/.test(field.name)) {
                    form.append($('<input>', {
                        type: "hidden",
                        name: field.name,
                        value: field.value
                    }));
                }
            }
        },

        change_company_id: function () {
            $.ceAdvancedImport('get_fields');
            $.ceAdvancedImport('get_images_prefix_path');
        },

        get_images_prefix_path: function () {
            var $elem = $('#advanced_import_images_path_prefix'),
                companies_image_directories = $elem.data('companiesImageDirectories'),
                company_id = company_selector.val();

            if ('relative_path' in companies_image_directories[company_id]) {
                $elem.text(companies_image_directories[company_id].relative_path);
            }
        },

        validate_modifier: function ($modifier, $modifier_control_group, show_notifications) {

            $.ceAjax('request', fn_url('import_presets.validate_modifier'), {
                data: {
                    modifier: $modifier.val(),
                    value: $modifier.data('caAdvancedImportOriginalValue'),
                    notify: show_notifications
                },
                hidden: true,
                method: 'post',
                callback: function (response) {
                    $modifier
                        .toggleClass('cm-failed-field', !response.is_valid)
                        .prop('data-ca-advanced-import-needs-validation', false);
                    $modifier_control_group.toggleClass('error', !response.is_valid);
                }
            });
        },

        init_modifiers_validation: function (modifiers) {

            var validation_timeout;

            modifiers
                .on('focusout', function () {
                    if (validation_timeout) {
                        clearTimeout(validation_timeout);
                    }

                    var $elm = $(this),
                        $elm_group = $elm.closest('.control-group');

                    $.ceAdvancedImport('validate_modifier', $elm, $elm_group, 'Y');
                })
                .on('keyup', function () {
                    if (validation_timeout) {
                        clearTimeout(validation_timeout);
                    }

                    var $elm = $(this),
                        $elm_group = $elm.closest('.control-group');

                    validation_timeout = setTimeout(function () {
                        $.ceAdvancedImport('validate_modifier', $elm, $elm_group, 'N');
                    }, 500);
                });

            $.ceEvent('on', 'ce.formpre_import_preset_update_form', function (form, clicked_elm) {
                if ($('.error', form).length) {
                    $.ceNotification('show', {
                        title: _.tr('error'),
                        message: _.tr('advanced_import.cant_save_preset_invalid_modifiers'),
                        type: 'E'
                    });
                    return false;
                }
            });
        },

        convert_xpath_to_tree: function (fields) {
            fields.each(function () {
                var $self = $(this),
                    attr_name = $self.text(),
                    depth = 0;

                var attr_path_parts = /^([^@]+)((@[^=]+)(="[^"]*")?)?/.exec(attr_name);
                if (attr_path_parts) {
                    var name_parts = attr_path_parts[1].split('/');
                    depth = name_parts.length - 2;
                    attr_name = name_parts.pop();

                    if (attr_path_parts[3]) {
                        depth++;
                        attr_name = attr_path_parts[2]
                            ? attr_path_parts[2]
                            : attr_path_parts[3];
                    }
                }

                $self
                    .css('paddingLeft', (nesting_padding_size * depth) + 'px')
                    .text(attr_name);
            });
        }
    };

    $.extend({
        ceAdvancedImport: function (method) {
            if (methods[method]) {
                return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
            } else {
                $.error('ty.advancedImport: method ' + method + ' does not exist');
            }
        }
    });

    $.ceEvent('on', 'ce.commoninit', function (context) {
        var preset = $('[data-ca-advanced-import-element="editor"]', context);
        if (preset.length) {
            preset_id = preset.data('caAdvancedImportPresetId');
            object_type = preset.data('caAdvancedImportPresetObjectType');
            company_selector = $('#elm_company_id', context);
            file_type_selector = $('[name="type_upload[]"]');
            file_selector = $('[name="file_upload[]"]');
            preset_name_selector = $('#elm_preset', context);
            xml_target_node_wrapper = $('#target_node').closest('.control-group');

            $.ceAdvancedImport('init_preset_editor');
        }

        var related_object_selectors = $('[data-ca-advanced-import-field-related-object-selector]', context);
        if (related_object_selectors.length) {
            $.ceAdvancedImport('init_related_object_selectors', related_object_selectors);
        }

        var fields_preview_opener = $('.import-preset__preview-fields-mapping', context);
        if (fields_preview_opener.length) {
            $.ceAdvancedImport('show_fields_preview', fields_preview_opener);
        }

        var import_start_button = $('.cm-advanced-import-start-import', context);
        if (import_start_button.length) {
            import_start_button.click();
        }

        var modifiers = $('[data-ca-advanced-import-element="modifier"]', context);
        if (modifiers.length) {
            $.ceAdvancedImport('init_modifiers_validation', modifiers);
        }

        var is_xml = $('[data-ca-advanced-import-preset-file-extension="xml"]', context);
        var fields = $('[data-ca-advanced-import-element="field"]', context);
        if (is_xml.length && fields.length) {
            $.ceAdvancedImport('convert_xpath_to_tree', fields);
        }
    });

    $(document).ready(function () {
        $('.advanced-import-file-editor-opener').on('click', function (e) {
            var $target = $(e.target),
                option_id = $target.data('targetInputId'),
                company_id = $target.closest('form').find('#elm_company_id').val(),
                relative_path = $('#' + option_id).val(),
                url = fn_url('import_presets.file_manager&path=' + relative_path + '&company_id=' + company_id),
                $finder_dialog = $('#' + option_id + '_dialog');

            $finder_dialog.ceDialog('destroy');
            $finder_dialog.empty();

            $finder_dialog.ceDialog('open', {
                'href': url,
                'title': _.tr('file_editor')
            });

            return false;
        });
    });
})(Tygh, Tygh.$);
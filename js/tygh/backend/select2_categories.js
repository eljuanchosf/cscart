(function(_, $) {
    $.ceEvent('on', 'ce.commoninit', function (context) {
        var $elems = $('.cm-object-categories-add', context),
            category_ids = [];

        if ($elems.length) {
            $.each($elems, function () {
                var value = $(this).val();

                if (!value) {
                    return;
                }

                if (!Array.isArray(value)) {
                    value = [value];
                }

                category_ids = category_ids.concat(value);
            });

            if (category_ids.length) {
                $.ceAjax('request', fn_url('categories.get_categories_list'), {
                    hidden: true,
                    caching: true,
                    data: { id: category_ids },
                    callback: function (response) {
                        var category_map = {};

                        if (typeof response.objects !== 'undefined') {
                            $.each(response.objects, function (key, item) {
                                category_map[item.id] = item;
                            });

                            $.each($elems, function (key, item) {
                                var $item = $(item),
                                    value = $item.val();

                                if (!value) {
                                    return;
                                }

                                if (!Array.isArray(value)) {
                                    value = [value];
                                }

                                $.each(value, function (key, id) {
                                    if (typeof category_map[id] !== 'undefined') {
                                        var category = category_map[id],
                                            $option = $item.find('option[value=' + id + ']');

                                        $option.text(category.text);
                                        $option.data('data', $.extend($option.data('data'), category));
                                    }
                                });

                                $item.trigger('change');
                            });
                        }
                    }
                });
            }
        }
    });

    $.ceEvent('on', 'ce.change_select_list', function (object, $container) {
        if ($container.hasClass('cm-object-categories-add') && object.data) {
            object.context = object.data.content;
        }
    });

    $.ceEvent('on', 'ce.select_template_selection', function (object, list_elm, $container) {
        if ($container.hasClass('cm-object-categories-add') && object.data) {
            if (object.data.disabled) {
                $(list_elm).find('.select2-selection__choice__remove').remove();
            }

            object.context = object.data.content;
        }
    });
}(Tygh, Tygh.$));

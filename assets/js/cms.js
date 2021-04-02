(function (exports, $) {
    "use strict";

    var props = {
        locale: null,
        routes: {},
        l10n: {},
        csrfToken: null,
        notifications: []
    };

    function CMS() { }

    CMS.prototype.setRoutes = function(routes) {
        props.routes = $.extend({}, props.routes, routes);
    };

    CMS.prototype.route = function(name) {
        if (name in props.routes) {
            return props.routes[name];
        }
        return null;
    };

    CMS.prototype.setLocale = function(locale) {
        props.locale = locale;
    };

    CMS.prototype.locale = function() {
        return props.locale;
    };

    CMS.prototype.setL10n = function(l10n) {
        props.l10n = l10n;
    };

    CMS.prototype.trans = function(key) {
        var data = props.l10n;
        var keyParts = key.split('.');
        for (var i = 0; i < keyParts.length; i++) {
            var keyConcrete = keyParts[i];
            if (keyConcrete in data) {
                data = data[keyConcrete]
            } else {
                data = null;
            }
        }

        return data;
    };

    CMS.prototype.setCsrfToken = function(token) {
        props.csrfToken = token;
    };

    CMS.prototype.csrfToken = function() {
        return props.csrfToken;
    };

    CMS.prototype.showNotify = function(notifications) {
        props.notifications = notifications;
    };

    CMS.prototype.TODO = function(msg) {
        console.warn("TODO: " + msg);
    };

    CMS.prototype.DEPRECATED = function(msg) {
        console.warn("Using deprecated functionality" + (msg ? '. ' + msg : ''));
    };

    CMS.prototype.ajaxGet = function(url, data) {
        data = data || {};
        return $.ajax({
            method: 'GET',
            url: url,
            data: data
        });
    };

    CMS.prototype.ajaxPost = function (url, data) {
        data = data || {};
        data['_token'] = this.csrfToken();
        return $.ajax({
            method: 'POST',
            url: url,
            data: data
        });
    };

    CMS.prototype.ajaxForm = function (form) {
        var $form = $(form);
        var method = $form.attr('method') ;
        var url = $form.attr('action');
        var data = $form.serializeArray();
        data.push({name: '_token', value: this.csrfToken()});
        return $.ajax({
            method: method,
            url: url,
            data: data
        });
    };

    CMS.prototype.notify = function(style, message) {
        var icon;
        if (style === "error") {
            icon = "fa fa-exclamation";
        } else if (style === "warning") {
            icon = "fa fa-warning";
        } else if (style === "success") {
            icon = "fa fa-check";
        } else if (style === "info") {
            icon = "fa fa-question";
        } else {
            icon = "fa fa-adjust";
        }

        $.notify({
            title: '',
            text: message,
            image: "<i class='" + icon + "'></i>"
        }, {
            style: 'metro',
            className: style,
            globalPosition: 'top right',
            showAnimation: "show",
            showDuration: 0,
            hideDuration: 0,
            autoHide: true,
            clickToHide: true
        });
    };


    var cms = new CMS();
    exports['CMS'] = cms;

    // -----------------------------------------------------------------------------------------------------------------

    $(function () {

        // Confirm actions
        // @new_version remove
        $('[data-cms-toggle="confirm"]').confirmation({
            title: cms.trans('confirmation.title'),
            btnOkLabel: cms.trans('confirmation.btn_ok'),
            btnCancelLabel: cms.trans('confirmation.btn_cancel'),
            btnOkClass: 'btn btn-sm btn-danger waves-effect waves-light',
            btnCancelClass: 'btn btn-sm btn-default waves-effect waves-light m-l-5',
            href: function (elem) {
                return $(elem).attr('href');
            }
        });


        // Reset button
        function resetForm($form) {
            $form.find('input:text, input:password, input:file, select, textarea').val('');
            $form.find('input:radio, input:checkbox').removeAttr('checked').removeAttr('selected');
        }
        $(':input[type=reset]').on('click', function () {
            var $form = $(this).parents('form');
            if ($form.length) {
                resetForm($form);
                $form.submit();
            }
        });


        // Group actions V2
        // @new_version rename ot cms-table, cms-action, etc...
        var $listTable = $('.ent-table');
        var $listAction = $('.ent-action');

        var $listCbs = $listTable.find('tbody input[type=checkbox]');
        var $listCbAll = $listTable.find('thead input[type=checkbox]');

        var onCbsChange = function() {
            var dis = !$listCbs.is(':checked');
            $listAction.find('button').prop('disabled', dis);
            var allChecked = $listCbs.not(':checked').length === 0;
            $listCbAll.prop('checked', allChecked);
        };
        $listCbs.on('change', onCbsChange);
        $listCbAll.on('change', function() {
            var checked = $(this).prop('checked');
            $listCbs.prop('checked', checked);
            onCbsChange();
        });

        //hidden.bs.modal
        var $listConfirm = $('.js-list-delete');
        var confirmCallback = null;
        $listConfirm.on('click', '.js-ok', function() {
            $listConfirm.modal('hide');
            if (confirmCallback !== null) {
                confirmCallback();
                confirmCallback = null;
            }
        });

        var showConfirm = function(many) {
            $listConfirm.find('.js-one').hide();
            $listConfirm.find('.js-many').hide();
            if (many) {
                $listConfirm.find('.js-many').show();
            } else {
                $listConfirm.find('.js-one').show();
            }
            $listConfirm.modal('show');
        };

        var sendAction = function(action, ids, noReload) {
            var href = $listAction.attr('data-url');
            var req = cms.ajaxPost(href, {
                action: action,
                ids: ids
            });
            req.done(function() {
                if (!noReload) {
                    location.reload();
                }
            });
            req.fail(function() {
                cms.notify('error', 'ERROR');
            });
        };
        var sendActionOld = function(action, ids) {
            var href = $listAction.attr('data-url');
            var f = document.createElement('form');
            f.setAttribute('method', 'post');
            f.setAttribute('action', href);
            var ia = document.createElement("input");
            ia.type = 'hidden';
            ia.name = 'action';
            ia.value = action;
            $.each(ids, function (_, val) {
                var i = document.createElement("input");
                i.type = 'hidden';
                i.name = 'ids[]';
                i.value = val;
                f.appendChild(i);
            });
            $("body").append(f);
            f.submit();
        };

        $listAction.on('click', '[data-action]', function(e) {
            e.preventDefault();

            var action = $(this).attr('data-action');
            var ids = [];
            $listCbs.filter(':checked').each(function() {
                var id = $(this).val();
                ids.push(id);
            });

            if (action === 'delete') {
                confirmCallback = function() {
                    sendAction(action, ids);
                };
                showConfirm(ids.length > 1);
            } else {
                sendAction(action, ids);
            }
        });

        $listTable.on('click', '[data-action]', function(e) {
            e.preventDefault();

            var $this = $(e.currentTarget);
            var action = $this.attr('data-action');
            var id = $this.closest('tr').find('input[type=checkbox]').val();
            var ids = [id];

            if (action === 'delete') {
                confirmCallback = function() {
                    sendAction(action, ids);
                };
                showConfirm(ids.length > 1);
            } else {

                var $group = $this.closest('.action-tuple');
                if ($group.length) {
                    $this.tooltip('hide');
                    $group.find('.fa').toggleClass('on off');
                    sendAction(action, ids, true);
                } else {
                    sendAction(action, ids);
                }
            }
        });

        // Table sortable
        var $sortable = $listTable.find('.sortable');

        $sortable.on('click', function () {
            var $this = $(this);
            var isAsc = $this.hasClass('asc');
            var isDesc = $this.hasClass('desc');
            $sortable.removeClass('asc desc');
            if (isDesc || (!isAsc && !isDesc)) {
                $this.addClass('asc');
                $this.trigger('asc.sortable');
            } else {
                $this.addClass('desc');
                $this.trigger('desc.sortable');
            }
            $this.trigger('sorted');
        });


        var $sort = $('.ent-filter .js-sort');
        if ($sort.length) {
            $('.ent-table').on('click', '.sortable', function () {
                var dir = $(this).hasClass('asc') ? 'asc' : 'desc';
                var by = $(this).attr('data-by');
                $sort.val(by + '-' + dir).closest('form').submit();
            });
            var sortStr = $sort.val();
            var sortArr = sortStr.split('-');
            $('.ent-table .sortable[data-by="' + sortArr[0] + '"]').addClass(sortArr[1]);
        }

        // Notifications
        $.each(props.notifications, function(_, tuple) {
            cms.notify(tuple[0], tuple[1]);
        });

        // Store / restore tabs state
        $('.nav-tabs a').on('shown.bs.tab', function(e) {
            if (history.pushState) {
                history.pushState(null, '', e.target);
            } else {
                location.hash = e.target.hash;
            }
        });
        var hash = location.hash;
        if (hash) {
            $('.nav-tabs a[href=' + hash + ']').tab('show');
        }

        // Forms field initialization
        var fOpen = $('.cms-form');
        if (fOpen.length) {
            fOpen.bootstrapValidator({
                excluded: [':disabled']
            });
        }
        var fDate = $('.cms-field-date');
        if (fDate.length) {
            // bootstrap-datepicker.readthedocs.io/en/latest/options.html
            fDate.datepicker({
                language: cms.locale(),
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true,
                //todayBtn: true,
                clearBtn: true,
                keyboardNavigation: false
            });
        }
        var fDateTime = $('.cms-field-datetime');
        if (fDateTime.length) {
            // http://eonasdan.github.io/bootstrap-datetimepicker/Options/
            fDateTime.datetimepicker({
                locale: cms.locale(),
                format: 'YYYY-MM-DD HH:mm:ss',
                //sideBySide: true,
                showClear: true,
                showTodayButton: true,
                keepInvalid: true,
                useCurrent: false,
                // like keyboardNavigation=false
                keyBinds: {
                    up: false,
                    down: false,
                    'control up': false,
                    'control down': false,
                    left: false,
                    right: false,
                    pageUp: false,
                    pageDown: false,
                    'delete': false,
                }
            });
            fDateTime.on('dp.change', function() {
                // validation fix
                $(this).trigger('input');
            });

        }
        var fSlug = $('.cms-field-slug');
        if (fSlug.length) {
            var fSlugSrc = fSlug.attr('data-src');
            if (fSlugSrc && fSlugSrc.length) {
                var fFrom = $('.cms-form :input[name="' + fSlugSrc + '"]');
                var fnTranslit = function (text) {
                    var pairs = {
                        // ru_RU
                        'а': 'a', 'б': 'b', 'в': 'v', 'г': 'g', 'д': 'd', 'е': 'e',
                        'ё': 'e', 'ж': 'j', 'з': 'z', 'и': 'i', 'й': 'i', 'к': 'k',
                        'л': 'l', 'м': 'm', 'н': 'n', 'о': 'o', 'п': 'p', 'р': 'r',
                        'с': 's', 'т': 't', 'у': 'u', 'ф': 'f', 'х': 'h', 'ц': 'c',
                        'ч': 'cs', 'ш': 'sh', 'щ': 'sx', 'ъ': '-', 'ы': 'y', 'ь': '',
                        'э': 'e', 'ю': 'ua', 'я': 'ya',
                        // uk_UA
                        'ґ': 'g', 'є': 'ye', 'і': 'i', 'ї': 'yi'
                    };
                    text = text.toLowerCase();
                    var res = '';
                    for (var i = 0; i < text.length; i++) {
                        if (text[i] in pairs) {
                            res += pairs[text[i]];
                        } else if (text[i].match(/^[0-9]+$/)) {
                            res += text[i];
                        } else {
                            res += text[i].match(/^[a-z_-]+$/) ? text[i] : '-';
                        }
                    }
                    res = res.replace(/cz(?=i|e|y|j)/g, "c");
                    res = res.replace(/[-]+/g, '-');
                    return res;
                };
                fFrom.on('input change', function () {
                    var val = $(this).val();
                    fSlug.val(fnTranslit(val));
                    fSlug.trigger('input');
                });
            }
        }
        var fSelect = $('.cms-field-select');
        if (fSelect.length) {
            if (fSelect.hasClass('select2')) {
                fSelect.filter('.select2').select2();
            }
        }
        var fTinyMce = $('.cms-field-tinymce');
        if (fTinyMce.length) {
            // docs @ https://www.tinymce.com/docs/demo/classic/
            fTinyMce.each(function() {
                var input = this;
                var height = $(input).attr('data-height') || 300;
                tinymce.init({
                    target: input,
                    //selector: "textarea.cms-field-tinymce",
                    theme: "modern",
                    branding: false,
                    height: height,
                    plugins: [
                        "advlist autolink link image lists charmap print preview hr anchor pagebreak",
                        "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
                        "save table contextmenu directionality emoticons template paste textcolor colorpicker textpattern imagetools",
                        "help"
                    ],
                    toolbar_items_size: 'small',
                    toolbar1: 'newdocument undo redo | cut copy paste | bold italic underline strikethrough subscript superscript | formatselect fontsizeselect forecolor backcolor removeformat ',
                    toolbar2: 'bullist numlist | alignleft aligncenter alignright alignjustify | outdent indent blockquote hr table | link unlink image media | charmap emoticons nonbreaking | code preview help',
                    menubar: false,
                    //statusbar: false,
                    relative_urls: false,
                    verify_html : false,
                    language: cms.locale(),
                    file_picker_callback: elFinderBrowser,
                    //add_form_submit_trigger: false,
                    //add_unload_trigger
                    init_instance_callback: function (editor) {
                        editor.on('Change', function () {
                            // fix validation
                            editor.save();
                            $(editor.getElement()).trigger('input');
                        });
                    }
                });
            });
        }

    });

    // https://github.com/Studio-42/elFinder/wiki/Integration-with-TinyMCE-4.x
    window.elFinderBrowser = function(callback, value, meta) {
        tinymce.activeEditor.windowManager.open({
            file: cms.route('elfinder'),
            title: 'elFinder',
            width: 900,
            height: 450,
            resizable: 'yes'
        }, {
            oninsert: function (file, elf) {
                var url, reg, info;
                // URL normalization
                url = file.url;
                reg = /\/[^/]+?\/\.\.\//;
                while(url.match(reg)) {
                    url = url.replace(reg, '/');
                }
                // Make file info
                info = file.name + ' (' + elf.formatSize(file.size) + ')';
                // Provide file and text for the link dialog
                if (meta.filetype == 'file') {
                    callback(url, {text: info, title: info});
                }
                // Provide image and alt text for the image dialog
                if (meta.filetype == 'image') {
                    callback(url, {alt: info});
                }
                // Provide alternative source and posted for the media dialog
                if (meta.filetype == 'media') {
                    callback(url);
                }
            }
        });
        return false;
    };

})(window, jQuery);
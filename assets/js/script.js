"use strict";

$(document).ready(function() {

    // @deprecated since v1.4.5
    // Show/Hide action
    $('.toggle-eye').click(function(){
        var self = this;
        var href = $(self).attr('data-href');
        var id = $(self).attr('data-id');
        var active = $(self).find('.fa-eye').is(":visible");

        var req = CMS.ajaxPost(href, {id: id, active: active});
        req.done(function (msg) {
            $(self).find('.fa-eye-slash').toggle();
            $(self).find('.fa-eye').toggle();
        });
    });

    // @deprecated since v1.4.5
    // Check/uncheck all
    $('.checkbox1').click(function () {
        if ($(this).is(':checked')) {
            $("input[type=checkbox]").prop('checked', true);
            $("input[type=checkbox]").attr('checked', 'checked');
        }
        else {
            $("input[type=checkbox]").prop('checked', false);
            $("input[type=checkbox]").removeAttr('checked', true);
        }
        $("input[type=checkbox]").trigger('change');
    });

    // @deprecated since v1.4.5
    // enable/disable group button
    $('input[type=checkbox]').click(function () {
        if ($('input[type=checkbox]').is(':checked')) {
            $(".delete-categ-btn").prop('disabled', false);
            $(this).attr('checked', 'checked');
        }
        else {
            $(".delete-categ-btn").prop('disabled', true);
            $(this).removeAttr('checked', true);
        }
    });
    var countChecked = function () {
        var n = $('input[type=checkbox]:checked').length;
        if (n <= 1) {
            $('.only-one-el').show();
            $('.group-el').hide();
        } else {
            $('.only-one-el').hide();
            $('.group-el').show();
        }
    };
    countChecked();
    $('input[type=checkbox]').on('click', countChecked);
    var countAllChecked = function () {
        var allChecked = $('input[type=checkbox]:checked:not(".checkbox1")').length;
        var allCheck = $('input[type=checkbox]:not(".checkbox1")').length;

        if (allCheck == allChecked) {
            $(".checkbox1").prop('checked', true);
        } else {
            $(".checkbox1").prop('checked', false);
        }
    };
    countAllChecked();
    $('input[type=checkbox]:not(".checkbox1")').on('click', countAllChecked);

    // @deprecated since v1.4.5
    $('div.form-group a[data-rel=activate]').on('click', function() {
        var self = this;
        var data = [];
        var href = $(self).attr('data-href');
        $('tbody tr td div.checkbox input:checked').each(function(index) {
            data.push($(this).val());
        });

        var req = CMS.ajaxPost(href, {data: data});
        req.done(function (msg) {
            location.reload();
        });
        req.fail(function (jqXHR, textStatus) {
            $('.container div.page-err').slideDown('slow', function () {
                setTimeout(function () {
                    $('.container div.page-err').slideUp('slow')
                }, 2000);
            });
        });
    });

    // @deprecated since v1.4.5
    $('div.form-group a[data-rel=deactivate]').on('click', function() {
        var self = this;
        var data = [];
        var href = $(self).attr('data-href');
        $('tbody tr td div.checkbox input:checked').each(function(index) {
            data.push($(this).val());
        });

        var req = CMS.ajaxPost(href, {data: data});
        req.done(function (msg) {
            location.reload();
        });
        req.fail(function (jqXHR, textStatus) {
            $('.container div.page-err').slideDown('slow', function () {
                setTimeout(function () {
                    $('.container div.page-err').slideUp('slow')
                }, 2000);
            });
        });
    });

    // @deprecated since v1.4.5
    // @note conflicts with menuitems delete btn
    $('.modal button.delete-yes').on('click', function () {
        var data = [];
        var href = $(this).attr('data-href');
        if (!href) {
            return;
        }
        $('tbody tr td div.checkbox input:checked').each(function (index) {
            data.push($(this).val());
        });

        var req = CMS.ajaxPost(href, {data: data});
        req.done(function (msg) {
            location.reload();
        });
        req.fail(function (jqXHR, textStatus) {
            $('.container div.page-err').slideDown('slow', function () {
                setTimeout(function () {
                    $('.container div.page-err').slideUp('slow')
                }, 2000);
            });
        });
    })
});

// GLOBAL
function send() {
    CMS.TODO('rewrite');

    var res = '';
    var text = document.getElementById('text').value;
    var transl = {
        А: 'a', а: 'a', Б: 'b', б: 'b', В: 'v', в: 'v', Г: 'g', г: 'g',
        Д: 'd', д: 'd', Е: 'e', е: 'e', Ё: '-', ё: '-', Ж: 'j', ж: 'j', З: 'z', з: 'z',
        И: 'i', и: 'i', Й: 'i', й: 'i', К: 'k', к: 'k', Л: 'l', л: 'l', М: 'm', м: 'm',
        Н: 'n', н: 'n', О: 'o', о: 'o', П: 'p', п: 'p', Р: 'r', р: 'r', С: 's', с: 's',
        Т: 't', т: 't', У: 'u', у: 'u', Ф: 'f', ф: 'f', Х: 'h', х: 'h', Ц: 'c', ц: 'с',
        Ч: 'cs', ч: 'cs', Ш: 'sh', ш: 'sh', Щ: 'sx', щ: 'sx', Ъ: '-', ъ: '-', Ы: '', ы: '',
        Ь: '', ь: '', Э: 'e', э: 'e', Ю: 'ua', ю: 'ua', Я: 'ya', я: 'ya', ' ': '-', '!': '-',
        '@': '-', '#': '-', '$': '-', '%': '-', '&': '-', '*': '-', '(': '-', ')': '-', '+': '-',
        '{': '-', '}': '-', ':': '-', '"': '-', ';': '-', "'": '-', ',': '-', '.': '-', '/': '-',
        '|': '-', '~': '-', '№': '-', '?': '-', '<': '-', '>': '-'
    };

    for (i = 0; i < text.length; i++) {
        if (transl[text[i]] != undefined) res += transl[text[i]];
        else res += text[i];
    }
    res = res.replace(/Cz(?=i|e|y|j|I|E|Y|J)/g, "C");
    res = res.replace(/cz(?=i|e|y|j|I|E|Y|J)/g, "c");
    document.getElementById('text_2').value = res;
}


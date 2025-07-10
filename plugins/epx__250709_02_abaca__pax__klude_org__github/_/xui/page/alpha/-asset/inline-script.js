//--------------------------------------------------------------------------------------------------------------------//
xui.form = {
    init() {
        $(document).on('change', 'form', function () {
            $(this).addClass('is-changed');
            $(this).parents('.x-if-changed').addClass('is-changed');

        });
        $(document).on('change', 'form input', function () {
            $(this).closest('form').addClass('is-changed');
            $(this).parents('.x-if-changed').addClass('is-changed');
            var namex = null
            if (!$(this).attr('name') && (namex = $(this).attr('x-name'))) {
                $(this).attr('name', namex);
            }
        });
        $(document).on('change', 'form select', function () {
            $(this).closest('form').addClass('is-changed');
            $(this).parents('.x-if-changed').addClass('is-changed');
            var namex = null
            if (!$(this).attr('name') && (namex = $(this).attr('x-name'))) {
                $(this).attr('name', namex);
            }
        });
        $(document).on('change', 'form textarea', function () {
            $(this).closest('form').addClass('is-changed');
            $(this).parenst('.x-if-changed').addClass('is-changed');
            var namex = null
            if (!$(this).attr('name') && (namex = $(this).attr('x-name'))) {
                $(this).attr('name', namex);
            }
        });
    }
}
//--------------------------------------------------------------------------------------------------------------------//
xui.sortable = {
        init() {
            $('.xui-sortable-pool').each(function () {
                $(this).data('sorter', Sortable.create(this, {
                    handle: '.xui-sortable-handle',
                    animation: 150,
                    onUpdate(evt) {
                        [].forEach.call(evt.from.getElementsByClassName('xui-sortable-body'), function (elem, index) {
                            var inp$ = $(elem).find('input.xui-sortable-index');
                            xui.TRACE_5 && console.log({
                                val: inp$.val(),
                                index,
                                elem
                            });
                            inp$.val(index);
                            inp$.trigger('change');
                        });
                    },
                    onAdd(evt) {
                        this.onUpdate(evt);
                    },
                }));
            });
        },
        refresh(el) {
            [].forEach.call(el.getElementsByClassName('xui-sortable-body'), function (elem, index) {
                var inp$ = $(elem).find('input.xui-sortable-index');
                xui.TRACE_5 && console.log({
                    val: inp$.val(),
                    index,
                    elem
                });
                inp$.val(index);
                inp$.trigger('change');
            });
        },
    },

    xui.sortable_alpha = {
        init() {
            $('.xui-sortable-alpha .xui-sortable-nest').each(function () {
                xui.sortable_alpha.sorter_init($(this));
            });
        },
        sorter_add(this$, html, nest$ = null) {
            if (!nest$) {
                nest$ = this$.closest('.xui-sortable-body').find('.xui-sortable-nest').first();
            }
            if (nest$?.length) {
                nest$.append(html);
                xui.sortable_alpha.sorter_refresh(nest$);
            }
        },
        sorter_init(this$) {
            if (!this$.data('sorter')) {
                this$.data('sorter', Sortable.create(this$[0], (this$.attr('x-is-nested')) ? {
                    group: 'nested',
                    handle: this$.attr('x-handle'),
                    animation: 150,
                    fallbackOnBody: true,
                    swapThreshold: 0.65,
                    onUpdate(evt) {
                        xui.sortable_alpha.sorter_refresh($(evt.from));
                    },
                    onAdd(evt) {
                        xui.sortable_alpha.sorter_refresh($(evt.from));
                    },
                } : {
                    handle: this$.attr('x-handle'),
                    animation: 150,
                    onUpdate(evt) {
                        xui.sortable_alpha.sorter_refresh($(evt.from));
                    },
                    onAdd(evt) {
                        xui.sortable_alpha.sorter_refresh($(evt.from));
                    },
                }));
            }
        },
        sorter_refresh_recurse(n$, sort) {
            var index = 0;
            xui.sortable_alpha.sorter_init(n$);
            n$.children('.xui-sortable-body').each(function () {
                var val = `${sort}/${index++}`;
                var inp$ = $(this).find('input.xui-sortable-index');
                inp$.val(val);
                inp$.trigger('change');
                var nestling$ = $(this).find('.xui-sortable-nest').first();
                if (nestling$.length) {
                    xui.sortable_alpha.sorter_refresh_recurse(nestling$, val);
                }
            });
        },
        sorter_refresh(this$) {
            var nest$ = this$.closest('.xui-sortable-root');
            this.sorter_refresh_recurse(nest$, '');
        },
        sorter_remove(this$) {
            var item$ = this$.closest('.xui-sortable-body');
            var nestling$ = item$.find('.xui-sortable-nest').first();
            if (nestling$.length) {
                nestling$.children('.xui-sortable-body').each(function () {
                    xui.sortable_alpha.sorter_remove($(this));
                });
            }
            var name = item$.attr('x-item-name');
            if (item$.hasClass('is-new')) {
                xui.TRACE_5 && console.log('deleting new');
            } else {
                var nest$ = this$.closest('.xui-sortable-root');
                xui.TRACE_5 && console.log('deleting existing');
                $(`<input type="hidden" name="${name}" value="">`)
                    .appendTo(nest$)
                    .trigger('change');
            }
            item$.remove();
        },
    }

//--------------------------------------------------------------------------------------------------------------------//
xui.pool = {
    init() {
        $(document).on('click', '.xui-pool .x-add-item', function () {
            var this$ = $(this);
            var pool$ = this$.closest('.xui-pool');
            var pool_name = pool$.data('name');
            console.log(this$);
            //pool$.append(html);
            pool$.each(function () {
                xui.sortable.refresh(this);
            });
        });
    },
    refresh(el) {
        [].forEach.call(el.getElementsByClassName('xui-sortable-body'), function (elem, index) {
            var inp$ = $(elem).find('input.xui-sortable-index');
            xui.TRACE_5 && console.log({
                val: inp$.val(),
                index,
                elem
            });
            inp$.val(index);
            inp$.trigger('change');
        });
    },
}
//--------------------------------------------------------------------------------------------------------------------//
xui.radio = {
    init() {
        $(document).on('x-init', '.xui-field-radio', function () {
            var f$ = $(this);
            if (f$.closest('.x-val').hasAttr('val')) {
                var val = f$.closest('.x-val').attr('val');
                f$.prop('checked', (val == f$.attr('value')) ? true : false);
                f$.trigger('change');
            }
        });
        $('.xui-field-radio').trigger('x-init');
    }
};
xui.checkbox = {
    init() {
        $(document).on('x-init', '.xui-field-checkbox', function () {
            var f$ = $(this);
            if (f$.hasAttr('val')) {
                var val = f$.attr('val');
                f$.prop('checked', (val == f$.attr('value')) ? true : false);
                f$.trigger('change');
            }
        });
        $('.xui-field-checkbox').trigger('x-init');
    }
};
//--------------------------------------------------------------------------------------------------------------------//
xui.select = {
    init() {
        $(document).on('x-init', '.xui-field-select', function () {
            var f$ = $(this);
            if (f$.hasClass('xui-data-source')) {
                if (f$.attr('datasource')) {

                }
            } else if (f$.hasClass('xui-data-source-async')) {
                var data_source_name = f$.attr('xui-data-source-name');
                xui.async_cache?.link('data-source', data_source_name, (e) => {
                    var cur_val = f$.val() ?? f$.attr('last-value');
                    console.log({
                        action: 'ds-update',
                        f$,
                        val: f$.val(),
                        cur_val,
                        data: e.detail.data
                    });
                    f$.val(cur_val);
                    f$.attr('last-value', cur_val);
                    f$.trigger('change');
                });
            } else {
                if (f$.hasAttr('val')) {
                    var val = f$.attr('val');
                    f$.val(val);
                    f$.trigger('change');
                }
            }
        });
        $('.xui-field-select').trigger('x-init');
    }
};
//--------------------------------------------------------------------------------------------------------------------//
xui.select2 = {
    init() {
        $(document).on('x-init', '.xui-field-select2', function () {
            var f$ = $(this);
            var parent$ = $(this).closest('.xui-select2-parent');
            var select_d = {};
            if (parent$.length) {
                select_d.dropdownParent = parent$;
            }
            if (f$.hasClass('xui-data-source')) {
                var data_source_name = f$.attr('xui-data-source-name');
                if (data_source_name) {
                    if (
                        xui.datasource[data_source_name] &&
                        typeof (xui.datasource[data_source_name].get_data) == 'function'
                    ) {
                        select_d.data = xui.datasource[data_source_name].get_data();
                        f$.select2(select_d);
                        console.log(select_d);
                    } else {

                    }
                }
            } else if (f$.hasClass('xui-data-source-async')) {
                var data_source_name = f$.attr('xui-data-source-name');
                xui.async_cache?.link('data-source', data_source_name, (e) => {
                    var cur_val = f$.val() ?? f$.attr('last-value');
                    console.log({
                        action: 'ds-update',
                        f$,
                        val: f$.val(),
                        cur_val,
                        data: e.detail.data
                    });
                    select_d.data = e.detail.data;
                    f$.select2(select_d);
                    f$.val(cur_val);
                    f$.attr('last-value', cur_val);
                    f$.trigger('change');
                });
            } else {
                f$.select2(select_d);
            }

            if (f$.hasAttr('val')) {
                var val = f$.attr('val');
                f$.val(val);
                f$.trigger('change');
            }
        });
        $('.xui-field-select2').trigger('x-init');
    }
};
//--------------------------------------------------------------------------------------------------------------------//
xui.summernote = {
    init() {
        $(document).on('x-init', '.xui-field-summernote', function () {
            var f$ = $(this);
            var placeholder = f$.attr('placeholder');
            f$.summernote({
                placeholder,
                tabsize: 2,
                height: 100
            });
        });
        $('.xui-field-summernote').trigger('x-init');
    }
};

$.fn.hasAttr = function (name) {
    return this.attr(name) !== undefined;
};
//--------------------------------------------------------------------------------------------------------------------//
xui.notifier = {

    init() {
        if (!$('.toast-container').length) {
            $('body').append(`<div class="toast-container bottom-0 end-0 position-absolute p-3"></div>`);
        }
    },

    alert(params) {
        var status_img;
        switch (params.status) {
            case 'ok': {
                status_img = `<svg class="bd-placeholder-img rounded me-2" width="20" height="20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" preserveAspectRatio="xMidYMid slice" focusable="false"><rect width="100%" height="100%" fill="#3aff00"></rect></svg>`
            }
            break;
        case 'fault': {
            status_img = `<svg class="bd-placeholder-img rounded me-2" width="20" height="20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" preserveAspectRatio="xMidYMid slice" focusable="false"><rect width="100%" height="100%" fill="#ff3a00"></rect></svg>`
        }
        break;
        default: {
            status_img = `<svg class="bd-placeholder-img rounded me-2" width="20" height="20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" preserveAspectRatio="xMidYMid slice" focusable="false"><rect width="100%" height="100%" fill="#003aff"></rect></svg>`
        }
        }

        var html = `
        <div class="toast fade" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true">
            <div class="toast-header">
            ${status_img}
            <strong class="me-auto">${params.title ?? 'Notice'}</strong>
            <small class="text-muted">${params.muted ?? ''}</small>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                ${params.message ?? 'Missing Message'}
            </div>
        </div>
    `;
        var toast$ = $(html).appendTo('.toast-container');
        toast$.toast('show');
    }
}

xui.image_container = {
    init(){
        $(document).on('input', '.x-image-container .slider', function (e) {
            var v = parseInt(this.value);
            //$(this).closest('.x-image-container').find('img').css('filter', 'grayscale('+v+'%)');
            var hex = v.toString(16).padStart(2, '0');
            var color = '#' + '' + hex + '' + hex + '' + hex;
            console.log({
                v: v,
                hex: hex,
                color: color
            });
            $(this).closest('.x-image-container').css('background-color', color);
        });                
    }
};
//--------------------------------------------------------------------------------------------------------------------//
xui.image_edit_field = {
    init() {
        $(document).on('change', 'input.x-field-file-input', function () {
            xui.TRACE_5 && console.log('input changed');
            var input = this;
            var field$ = $(this).closest('.x-field-file');
            field$.find('.x-view-options').remove();
            if(input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    field$.find('.x-preview-img').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
                field$.addClass('xui-feature-exists');
                field$.find('.x-field-remove-input').prop('disabled',true);
            } else {
                xui.TRACE_5 && console.log('input removed');
                field$.find('.x-preview-img').attr('src', '');
                field$.removeClass('xui-feature-exists');
                field$.find('.x-field-remove-input').prop('disabled', false);
            }
        });

        ['drag', 'dragstart', 'dragend', 'dragover', 'dragenter', 'dragleave', 'drop'].forEach(event_name => {
            $(document).on(event_name, '.x-field-file', function (e) {
                e.preventDefault();
                e.stopPropagation();
            });
        }, false);

        ['dragover', 'dragenter'].forEach(event_name => {
            $(document).on(event_name, '.x-field-file', function (e) {
                $(this).addClass('is-dragover');
            });
        }, false);

        ['dragleave', 'dragend', 'drop'].forEach(event_name => {
            $(document).on(event_name, '.x-field-file', function (e) {
                $(this).removeClass('is-dragover');
            });
        }, false);

        $(document).on('drop', '.x-field-file', function (e) {
            var droppedFiles = e.originalEvent.dataTransfer.files;
            file_input$ = $(this).find('input.x-field-file-input');
            if(droppedFiles.length){
                xui.TRACE_5 && console.log({'files_dropped': droppedFiles.length});
                file_input$[0].files = droppedFiles;
                file_input$.trigger('change');
            }
        });
        
        $(document).on('click', '.x-field-file .x-remove', function (e) {
            xui.TRACE_5 && console.log('remove clicked');
            var field$ = $(this).closest('.x-field-file');
            var input$ = field$.find('.x-field-file-input');
            var field_sub_values$ = field$.find('.x-field-sub-value');
            field_sub_values$.prop('disabled',true);
            input$.val('');
            input$.trigger('change');
        });
        
    },
};

$(() => {
    //console.log('image_editor');
    xui.image_edit_field.init();
    xui.image_container.init();
});


$.fn.wait = function (time, type) {
    time = time || 1000;
    type = type || "fx";
    return this.queue(type, function () {
        var self = this;
        setTimeout(function () {
            $(self).dequeue();
        }, time);
    });
};

$.fn.xui_drawer = function (show) {
    if (show) {
        $(this).addClass('enable')
            .delay(100)
            .queue(function () {
                $(this).addClass('on').dequeue();
            });
        document.documentElement.style.overflowY = 'hidden';
        document.documentElement.style.overflowX = 'hidden';
    } else {
        document.documentElement.style.overflowY = 'auto';
        document.documentElement.style.overflowX = 'auto';
        $(this)
            .removeClass('on')
            .delay(500)
            .queue(function () {
                $(this).removeClass('enable').dequeue();
            });
    }
};


//* for all the navs
$(() => {
    (xui.TRACE_7) && console.log('setting_active_nav');
    (xui.TRACE_1) && console.log({
        location: window.location
    });
    var hits = [];
    document.querySelectorAll('.xui-nav-indicator')
        .forEach((r) => {
            var href = r.getAttribute('href');
            r.classList.remove('active');
            r.classList.remove('active-parent');
            if (window.location.href === href) {
                r.classList.add('active');
                hits.push(r.getAttribute('navpath'));
            } else if (window.location.href.startsWith(href)) {
                r.classList.add('active-parent');
                hits.push(r.getAttribute('navpath'));
            }
        });
    //console.log(hits);
    document.querySelectorAll('.xui-nav-indicator')
        .forEach((r) => {
            if (!(r.classList.contains('active') || r.classList.contains('active-parent'))) {
                var navpath = r.getAttribute('navpath');
                hits.forEach((h) => {
                    if (h && h.startsWith(navpath)) {
                        r.classList.add('active-parent');
                    }
                });
            }
        });
});


$(() => {
    function scroll_shadow_on_sticky_top() {
        if (this.scrollTop) {
            var svert = 100 * this.scrollTop / (this.scrollHeight - this.clientHeight);;
            if (svert <= 0) {
                $(this).removeClass('scroll-top-active');
            } else {
                $(this).addClass('scroll-top-active');
            }
            if (svert >= 100) {
                $(this).removeClass('scroll-bottom-active');
            } else {
                $(this).addClass('scroll-bottom-active');
            }
        } else {
            $(this).removeClass('scroll-top-active');
        }
        1 && console.log({
            svert,
            a: this.scrollTop,
            b: this.scrollHeight,
            c: this.clientHeight
        });

    }

    $('.scrollable-shadow-on-sticky-top').scroll(scroll_shadow_on_sticky_top);
    $('.scrollable-shadow-on-sticky-top').each(scroll_shadow_on_sticky_top);

    xui.sortable.init();
    xui.sortable_alpha.init();
    xui.pool.init();
    xui.radio.init();
    xui.checkbox.init();
    xui.select.init();
    xui.select2.init();
    xui.summernote.init();
    xui.form.init();

    xui.notifier.init();
    $('.toast').toast('show');

    $('form').submit(function () {
        console.log('Loading');
        if(!$(this).data('disable-global-spinner')){
            $('.xui-loading-indicator').show();
        }
    });
});
$.fn.extend({
    cluster__init() {
        $(this).find('.xui-sortable-nest').each(function () {
            $(this).cluster__nest_setup();
        });
    },
    cluster__add() {
        var this$ = $(this);
        var cluster$ = this$.closest('.xui-cluster');
        var nest$ = null;
        if (this$.hasClass('xui-cluster')) {
            console.log({
                v: 'root',
                nest$,
                this$
            });
            nest$ = cluster$.find('.xui-sortable-root').first();
        } else {
            nest$ = this$.closest('.xui-sortable-body').find('.xui-sortable-nest').first();
            console.log({
                v: 'sub',
                nest$,
                this$
            });
        }
        if (nest$?.length) {
            var struct__fn = null;
            if (struct__fn = cluster$.attr('x-struct-fn')) {
                console.log(struct__fn);
                var html = window[struct__fn](Date.now());
                nest$.append(html);
                nest$.cluster__refresh();
            }
        }
    },
    cluster__nest_setup() {
        var this$ = $(this);
        var cluster$ = $(this).closest('.xui-cluster');
        if (!this$.data('sorter')) {
            this$.data('sorter', Sortable.create(this$[0], (cluster$.attr('x-is-nested')) ? {
                group: 'nested',
                handle: cluster$.attr('x-handle'),
                animation: 150,
                fallbackOnBody: true,
                swapThreshold: 0.65,
                onUpdate(evt) {
                    $(evt.from).cluster__refresh();
                },
                onAdd(evt) {
                    $(evt.from).cluster__refresh();
                },
            } : {
                handle: cluster$.attr('x-handle'),
                animation: 150,
                onUpdate(evt) {
                    $(evt.from).cluster__refresh();
                },
                onAdd(evt) {
                    $(evt.from).cluster__refresh();
                },
            }));
            console.log(this$);
        }
    },
    cluster__refresh() {
        var this$ = $(this);
        var nest$ = this$.closest('.xui-sortable-root');
        var recurse = function (n$, sort) {
            var index = 0;
            n$.cluster__nest_setup();
            n$.children('.xui-sortable-body').each(function () {
                var val = `${sort}/${index++}`;
                var inp$ = $(this).find('input.xui-sortable-index');
                inp$.val(val);
                inp$.trigger('change');
                var nestling$ = $(this).find('.xui-sortable-nest').first();
                if (nestling$.length) {
                    recurse(nestling$, val);
                }
            });
        };
        recurse(nest$, '');
    },
    cluster__remove() {
        var this$ = $(this);
        var item$ = this$.closest('.xui-sortable-body');
        var nestling$ = item$.find('.xui-sortable-nest').first();
        if (nestling$.length) {
            nestling$.children('.xui-sortable-body').each(function () {
                $(this).cluster__remove();
            });
        }
        var name = item$.attr('x-item-name');
        if (item$.hasClass('is-new')) {
            xui.TRACE_5 && console.log('deleting new');
        } else {
            var nest$ = this$.closest('.xui-sortable-root');
            xui.TRACE_5 && console.log('deleting existing');
            $(`<input type="hidden" name="${name}" value="">`)
                .appendTo(nest$)
                .trigger('change');
        }
        item$.remove();
    },
});

$(() => {
    $('.xui-cluster').cluster__init();
    $(document).on('click', '.xui-cluster-bravo button.x-item-remove', function () {
        $(this).cluster__remove();
    });
});

$(() => {
    $('.xui-loading-indicator').hide();
});

// enabling disables bfcache (see https://web.dev/bfcache/)
/*
    window.addEventListener('beforeunload', (event) => {
        if (pageHasUnsavedChanges()) {
            event.preventDefault();
            return event.returnValue = 'Are you sure you want to exit?';
        }
    });
*/

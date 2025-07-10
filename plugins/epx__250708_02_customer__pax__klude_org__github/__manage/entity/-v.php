<?php include "_/xui/page/alpha/bow-v.php" ?>
<div class="d-flex flex-column flex-nowrap" style="max-height:100vh; height:100vh;">
    <div class="flex-fill overflow-autoscrollable-shadow-on-sticky-top" style="height: 1px">
        <!-- OPEN _/xui/table/alpha::instance  -->
        <div class="d-flex flex-column flex-nowrap h-100">
            <div class="flex-shrink-1">
                <div class="d-flex justify-content-between pt-2 pb-2 bg-light border-bottom">
                    <div class="align-self-start me-2">
                    </div>
                    <div class="flex-fill">
                        <span class="h4"><?=$this->vars['table/header'] ?? 'Untitled'?></span>
                    </div>
                    <div class="align-self-end">
                        <div class="d-flex justify-content-between">
                            <?php if($this->vars['table/result']['on__export'] ?? false): ?>
                            <form class="x-table-params-form">
                                <input hidden class="x-table-params-field x-table-export-val" name="--p[export]" value="">
                            </form>
                            <div class="btn-group me-1">
                                <button type="button" class="btn btn-sm btn-outline-success" onclick="xui__table_export()"><?=$this->vars['table/button_export']['text'] ?? 'Export'?></button>
                            </div>
                            <?php endif ?>
                            <?php if($this->vars['table/button_new']['href'] ?? false): ?>
                            <div class="btn-group me-1">
                                <a class="btn btn-sm btn-outline-success" href="<?=$this->vars['table/button_new']['href'] ?? 'javascript:void(0)'?>"><?=$this->vars['table/button_new']['text'] ?? 'New'?></a>
                            </div>
                            <?php endif ?>
                            <form class="d-inline-block x-table-params-form">
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm x-table-params-field" name="--p[search]" placeholder="Search" value="<?= htmlspecialchars($this->vars['table/params']['search'] ?? '') ?>" aria-label="Search" aria-describedby="<?=$uid1 = \uniqid()?>">
                                    <button class="btn btn-sm btn-outline-secondary" type="submit" id="<?=$uid1?>"><i class="bi bi-search"></i></button>
                                    <a class="btn btn-sm btn-outline-danger" href="<?=\_\CTLR_URL?>">&times;</a>
                                </div>
                            </form>
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-default" onclick="window.location.reload()"><span class="bi bi-bootstrap-reboot"></span></button>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex-fill overflow-auto" style="height: 1px">

            <?php $this->inset__prt() ?>
            
            </div>
            <div class="flex-shrink-1">
                <div class="d-flex justify-content-between pt-2 pb-2 bg-light border-top">
                    <div class="align-self-start px-2">
                        <!-- Page Size Selection -->
                        <div class="d-flex justify-content-between align-items-center">
                            <form class="x-table-params-form">
                                <select class="form-select form-select-sm d-inline-block w-auto x-table-params-field" id="page_sz" name="--p[page_sz]" onchange="$(this).closest('form').submit()">
                                    <?php $this->paginator_entries__prt() ?>
                                </select>
                                <span class="ms-1">Entries</span>
                            </form>
                        </div>
                    </div>
                    <div class="flex-fill text-center">
                        <?php $this->paginator_stats__prt() ?>
                    </div>
                    <div class="align-self-end px-2">
                        <form class="x-table-params-form" hidden>
                            <input hidden class="x-table-params-field x-table-page_no" name="--p[page_no]" value="<?=$this->vars['table/result']['meta']['page_no']?>">
                        </form>
                        <?php $this->paginator_nav__prt() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function xui__table_set_page(i){
        var this$ = $(`input.x-table-params-field.x-table-page_no`);
        this$.val(i);
        this$.closest('form').submit();
    }
    function xui__table_export(el){
        var this$ = $('.x-table-export-val').val('1');
        this$.closest('form').submit();
    }
    
    $(() => {
        $(document).on('submit','.x-table-params-form',function(){
            var html = '';
            $('.x-table-params-field').each(function(){
                var name = $(this).attr('name');
                var val = $(this).val();
                html += `<input hidden name="${name}" value="${val}">`;
            });
            console.log({html});
            $(`<form method="GET">${html}</form>`).appendTo('body').submit();
            return false;
        });
    })
</script>
<!-- CLOSE _/xui/table/alpha::instance  -->
<?php include "_/xui/page/alpha/stern-v.php" ?>

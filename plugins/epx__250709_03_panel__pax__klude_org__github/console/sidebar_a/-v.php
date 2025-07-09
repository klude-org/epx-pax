<?php 
    $navs = []; //$this->navs();
    $this->ui = \_\i\flex\alpha::_();
    $this->ui->get = function($n){
        return $this[$n] ?? null;
    };
?>
<style>
    <?php include '-v.css' ?>
</style>
<!-- <main class="d-flex flex-nowrap"> -->
    <div class="d-flex flex-column flex-shrink-0 ps-1 py-2 bg-light" style="width: 200px;">
        <a href="<?=$this->ui->get('sidemenu/header/href')?:o()->site_url?>" class="d-flex justify-content-center pb-3 link-body-emphasis text-decoration-none border-bottom">
            <span class="sidemenu-header-icon"><?=$this->ui->get('sidemenu/header/icon')?:''?></span>
            <span class="sidemenu-header-label fs-5 fw-semibold"><?=$this->ui->get('sidemenu/header/label')?:'Untitled'?></span>
        </a>
        <div class="w3-bar-block position-relative flex-column list-unstyled ps-0 flex-grow-1">
            <?php foreach($navs['navs']['m'] ?? [] as $k => $v): ?>
            <?php if(\is_array($v) && count($v) > 1): if(\is_array($v) && $v['#']->is_active ?? null){ $tab = $v; }  ?>
            <a class="w3-bar-item xui-parent-node w3-button <?=($n = $v['#']->is_active ?? null) ? (($n == 1) ? 'w3-green' : 'w3-pale-green') : ''?>" href="<?=$v['#']->href??'javascript:;'?>">
                <?=$v['#']->label ?? ''?>
            </a>
            <span class="w3-button xui-dropdown-hover <?=($n = $v['#']->is_active ?? null) ? (($n == 2) ? 'w3-green' : '') : ''?>">
                <i class="fa fa-caret-down"></i>
            </span>
            <div class="w3-dropdown-content w3-bar-block w3-card-4">
                <?php foreach($v['m'] ?? [] as $k1 => $v1): if(\is_array($v1)): if(count($v1) > 1 && $v1['#']->is_active ?? null){ $tab = $v1; }?>
                <a class="w3-bar-item w3-button <?=($v1['#']->is_active ?? null) ? 'w3-green' : ''?>" href="<?=$v1['#']->href??'javascript:;'?>"><?=$v1['#']->label ?? 'Untitled'?></a>
                <?php endif; endforeach ?>
            </div>
            <?php else: ?>
            <a class="w3-bar-item w3-button <?=($v['#']->is_active ?? null) ? 'w3-green' : ''?>" href="<?=$v['#']->href??'javascript:;'?>"><?=$v['#']->label ?? 'Untitled'?></a>
            <?php endif ?>
            <?php endforeach ?>
        </div>
        <hr>
        <div class="dropdown flex-grow-0">
            <a href="#" class="d-flex align-items-center link-body-emphasis text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="" style="object-fit:cover" alt="" width="32" height="32" class="rounded-circle me-2">
                <strong><?="USER"?></strong>
            </a>
            <ul class="dropdown-menu text-small shadow">
                <li><a class="dropdown-item" href="<?=o()->base_url.'?--auth=panels'?>">Change Panel</a></li>
                <li><a class="dropdown-item" href="<?=o()->base_url.'?--auth=roles'?>">Change Role</a></li>
                <li><a class="dropdown-item" href="<?=o()->base_url.'?--auth=settings'?>">Settings</a></li>
                <li><a class="dropdown-item" href="<?=o()->base_url.'?--auth=profile'?>">Profile</a></li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item" href="<?=o()->base_url.'?--signout'?>">Sign out</a></li>
            </ul>
        </div>
    </div>
<!-- </main> -->
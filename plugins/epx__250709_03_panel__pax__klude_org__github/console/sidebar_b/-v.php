<?php 
    
?>
<div class="d-flex flex-column flex-nowrap" style="max-height:100vh; height:100vh;">
    <div class="flex-grow-1 d-flex">
        <!-- Sidebar BEGIN -->
        <div class="flex-shrink-1 d-flex flex-column flex-nowrap border-end" style="max-width:150px">
            <!-- Sidebar Header -->
            <div class="xui-sidenav-header flex-shrink-1 d-flex px-4 border-bottom">
                <a href="<?=o()->env->base_url?>" class="d-flex justify-content-center text-decoration-none py-2">
                    <span class="sidenav-header-icon"></span>
                    <span class="sidenav-header-label fs-5 fw-semibold ">Energy CRM</span>
                </a>
            </div>
            <!-- Sidebar Content -->
            <div class="xui-sidenav-wrapper flex-fill" style="height: 1px"><!-- https://stackoverflow.com/a/75377904 -->
                <div class="xui-sidenav w3-bar-block position-relative flex-column list-unstyled ps-0 flex-grow-1">
                    <a class="w3-bar-item w3-button xui-nav-indicator" href="<?=o()->env->base_url?>/console" navpath="">Console</a>
                    <a class="w3-bar-item w3-button xui-nav-indicator" href="<?=o()->env->base_url?>/connection" navpath="">Connections</a>
                    <a class="w3-bar-item w3-button xui-nav-indicator" href="<?=o()->env->base_url?>/client" navpath="">Clients</a>
                    <a class="w3-bar-item w3-button xui-nav-indicator" href="<?=o()->env->base_url?>/retailer" navpath="">Retailers</a>
                    <a class="w3-bar-item w3-button xui-nav-indicator" href="<?=o()->env->base_url?>/retailer_account" navpath="">Retailer Accounts</a>
                    <a class="w3-bar-item w3-button xui-nav-indicator" href="<?=o()->env->base_url?>/retailer_subaccount" navpath="">Retailer Sub Accounts</a>
                    <a class="w3-bar-item w3-button xui-nav-indicator" href="<?=o()->env->base_url?>/client_associate" navpath="">Client Associates</a>
                    <a class="w3-bar-item w3-button xui-nav-indicator" href="<?=o()->env->base_url?>/client_connection_group" navpath="">Connection Groups</a>
                    <a class="w3-bar-item w3-button xui-nav-indicator" href="<?=o()->env->base_url?>/settings" navpath="">Settings</a>
                </div>
            </div>
            <!-- Sidebar Footer -->
            <div class="xui-sidenav-footer dropdown flex-shrink-1 dropup border-top py-2">
                <a href="#" class="d-flex align-items-center px-2 text-decoration-none dropdown-toggle dropup" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="" style="object-fit:cover" onerror="this.style.display = 'none'" alt="" width="32" height="32" class="rounded-circle me-2">
                    <span class="fs-5 fw-semibold">User</span>
                    <span class="flex-grow-1">&nbsp;</span>
                </a>
                <ul class="dropdown-menu text-small shadow">
                    <li><a class="dropdown-item" href="?--signout">Sign out</a></li>
                </ul>
            </div>
        </div>
        <!-- Sidebar END -->
        <!-- Main BEGIN -->
        <div class="flex-fill d-flex flex-column flex-nowrap" style="width: 1px">
            <!-- https://stackoverflow.com/a/75377904 -->
            <!-- Content BEGIN -->
            <div class="flex-fill  " style="height: 1px">
                <?=$__INSET__ ?? ''?>
            </div>
            <!-- Content END -->
        </div>
        <!-- Main END -->
    </div>
</div>
<div class="toast-container bottom-0 end-0 position-fixed p-3">
</div>
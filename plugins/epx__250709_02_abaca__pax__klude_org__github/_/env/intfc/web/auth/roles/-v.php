<?php 
    if(1){
        $icon = ($f = \_::file('xui-custom-00.lib/-pub/img/logo-block-sm','.ico|.png|.jpg|.svg'))
            ? 'data:image/png;base64,'.base64_encode(file_get_contents($f))
            : ''
        ;
    } else {
        $icon = \_::file('xui-custom-00.lib/-pub/img/logo-block-sm','.ico|.png|.jpg|.svg');
    }
  
    $select_options = $this->get_role_select_options();
    
?>

<?php include "_/xui/page/basic/bow-v.php" ?>
<script>
    document.title = 'User Roles';
    //* force window top
    if(window.top != window){
        window.top.location.reload();
    }
</script>
<style>
    html,
    body {
        height: 100%;
    }

    .page {
        height: 100%;
        display: flex;
        align-items: center;
        padding-top: 40px;
        padding-bottom: 40px;
        background-color: #f5f5f5;
    }

    .form-signin {
        max-width: 330px;
        padding: 15px;
    }

    .form-signin .form-floating:focus-within {
        z-index: 2;
    }

    .form-signin input[type="email"] {
        margin-bottom: -1px;
        border-bottom-right-radius: 0;
        border-bottom-left-radius: 0;
    }

    .form-signin input[type="password"] {
        margin-bottom: 10px;
        border-top-left-radius: 0;
        border-top-right-radius: 0;
    }
    
    .active a {
        color: white;
    }
</style>    
<div class="page text-center">
    <main class="form-signin w-100 m-auto">
        <form action="" method="POST">
            <input type="hidden" name="--action" value="change">
            <input type="hidden" name="--auth" value="change_roles">
            <input type="hidden" name="--csrf" value="<?=\_\CSRF?>">
            <img class="mb-4" src="<?=$icon?>" alt="" width="72">
            <h1 class="h3 mb-3 fw-normal">Select A Role</h1>
            <div class="list-group">
                <?php foreach($select_options as $v): ?>
                    <a  class="list-group-item list-group-item-action <?=($n = $v['is_selected'] ?? false) ? 'active' : ''?>"
                        href="<?=$v['href']?>"
                        aria-current="<?=$n ? 'true' : 'false'?>">
                        <?=$v['label'] ?? 'Unlabelled Role'?>
                    </a>
                <?php endforeach ?>
            </div>
            <p class="mt-5 mb-3 text-muted"><a href="https://klude.com.au">klude.com.au</a> &copy; 2017–2025</p>
        </form>
    </main>
</div>
<?php include "_/xui/page/basic/stern-v.php" ?>

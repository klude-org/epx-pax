    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Brian Pinto">
    <meta name="generator" content="Epx-PHP-Pax-Neo 1.18">

    <title><?=\implode(' | ',\array_filter([
        $this->xui->vars['browser/title'] ?? '',
        $this->xui->vars['panel/dob']['browser/title'] ?? '',
    ])) ?: 'Untitled'?></title>
    <!-- Favicons -->
    <!-- <link rel="icon" href="-pub/img/favicon.png"> -->
    <script>
        <?php if(\defined('_\CSRF')): ?>
        const X_CSRF = "<?=\_\CSRF?>";
        <?php endif ?>
        if (typeof xui === 'undefined') {
            xui = {
                datasource: {}
            };
        }

        if (typeof xui.TRACE === 'undefined') {
            xui.TRACE = 6;
            xui.TRACE_1 = ((xui.TRACE ?? 0) >= 1);
            xui.TRACE_2 = ((xui.TRACE ?? 0) >= 2);
            xui.TRACE_3 = ((xui.TRACE ?? 0) >= 3);
            xui.TRACE_4 = ((xui.TRACE ?? 0) >= 4);
            xui.TRACE_5 = ((xui.TRACE ?? 0) >= 5);
            xui.TRACE_6 = ((xui.TRACE ?? 0) >= 6);
            xui.TRACE_7 = ((xui.TRACE ?? 0) >= 7);
            xui.TRACE_8 = ((xui.TRACE ?? 0) >= 8);
            xui.TRACE_9 = ((xui.TRACE ?? 0) >= 9);
            (xui.TRACE_1) && console.log({
                xui
            });
        }
    </script>

    <!-- jQuery -->
    <script src="//code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

    <!-- jQuery UI -->
    <link href="//code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css" />
    <script src="//code.jquery.com/ui/1.13.2/jquery-ui.min.js" integrity="sha256-lSjKY0/srUM9BE3dPm+c4fBo1dky2v27Gdjm2uoZaL0=" crossorigin="anonymous"></script>

    <!-- W3 CSS (keep it above bootstrap) -->
    <link rel="stylesheet" href="//www.w3schools.com/w3css/4/w3.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Bootstrap minified CSS -->
    <link href="//cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Bootstrap minified JavaScript -->
    <script src="//cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <!-- <link href="<?=\_\SITE_URL?>/--epx/xui-legacy-01.lib/xui__bootstrap_glyphicons/-pub/css/bootstrap-glyphicons.css" rel="stylesheet" onerror="xui?.debug?.plugin_load_error('style',this,event)"> -->

    <!-- Select2 css/js -->
    <link href="//cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="//cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- <link href="<?=\_\SITE_URL?>/--epx/-pub/treesortable-00/treeSortable.css" rel="stylesheet">
    <script src="<?=\_\SITE_URL?>/--epx/-pub/treesortable-00/treeSortable.js"></script> -->

    <!-- Latest Sortable -->
    <script src="//SortableJS.github.io/Sortable/Sortable.js"></script>

    <!-- Datatables.js -->
    <link href="//cdn.datatables.net/1.12.1/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <script src="//cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
    <script src="//cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script>

    <!-- Quill.js -->
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>

    <link href="//cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-bs5.min.css" integrity="sha512-ngQ4IGzHQ3s/Hh8kMyG4FC74wzitukRMIcTOoKT3EyzFZCILOPF0twiXOQn75eDINUfKBYmzYn2AA8DkAk8veQ==" crossorigin="anonymous" reffrrerpolicy="no-reffrrer" rel="stylesheet" />
    <script src="//cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-bs5.min.js" integrity="sha512-6F1RVfnxCprKJmfulcxxym1Dar5FsT/V2jiEUvABiaEiFWoQ8yHvqRM/Slf0qJKiwin6IDQucjXuolCfCKnaJQ==" crossorigin="anonymous" reffrrerpolicy="no-reffrrer"></script>

    <!-- Codemirror -->
    <!-- <link href="https://codemirror.net/1/css/docs.css" rel="stylesheet" /> -->
    <script src="https://codemirror.net/1/js/codemirror.js"></script>
    
    <style><?php include '-asset/inline-style.css' ?></style>
    
    <style>
        .xui-sidenav-footer,
        .xui-sidenav-header {
            background-color:#050e2b;
            color: #ffffff;
        }
        .xui-sidenav-footer a,
        .xui-sidenav-header a {
            color: #ffffff;
        }
        
        .xui-sidenav-footer .dropdown-menu a {
            color: #050e2b;
        }
    </style>    
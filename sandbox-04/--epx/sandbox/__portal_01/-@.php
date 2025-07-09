<?php 
    $o = (object)[ 'page' => (object) [], 'window' => (object) [], 'header' => (object) [], 'footer' => (object) [], ];
    $o->window->title = 'Menu';
    $o->window->icon = "";
    $o->header->label = 'Menu';
    $o->header->icon = "<i class=\"bi bi-grid-fill\"></i>";
    $o->footer->label = "brian.pinto@cybernetworks.com.au";
    $o->footer->icon = "https://github.com/mdo.png";
    $o->footer->nav = \json_decode(\file_get_contents(\_\path_here('footer_nav.json')));
    $o->page->nav = \json_decode(\file_get_contents(\_\path_here('nav.json')));
?>
<!DOCTYPE html>
<html lang="en">
<!-- https://codepen.io/Zodiase/pen/qmjyKL -->
<head>
    <meta charset="UTF-8">
    <title>Resizable Sidebar</title>
    <script>xui = { datasource:{}, };</script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.4/lodash.min.js'></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.0/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>    
   
    <style>
        html {
            height: 100%;
        }

        body {
            box-sizing: border-box;
            height: 100%;
            margin: 0;
            padding: 0;
            background-color: black;
        }
        
        /*############################################################################*/
        /* scrollbar */
        ::-webkit-scrollbar {
            width: 5px;
        }

        ::-webkit-scrollbar-track {
            background: #888;
        }

        ::-webkit-scrollbar-thumb {
            background: #e1e1e1;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #f5f5f5;
        }        

        .xui-page {
            height: 100%;
            overflow: hidden;
            background-color: white;
        }
        /*############################################################################*/

        .xui-page {
            display: -ms-flexbox;
            display: -webkit-flex;
            display: flex;
            -webkit-flex-direction: row;
            -ms-flex-direction: row;
            flex-direction: row;
            -webkit-flex-wrap: nowrap;
            -ms-flex-wrap: nowrap;
            flex-wrap: nowrap;
            -webkit-justify-content: flex-start;
            -ms-flex-pack: start;
            justify-content: flex-start;
            -webkit-align-content: stretch;
            -ms-flex-line-pack: stretch;
            align-content: stretch;
            -webkit-align-items: stretch;
            -ms-flex-align: stretch;
            align-items: stretch;
        }


        .xui-resize-handle--x {
            -webkit-flex: 0 0 auto;
            -ms-flex: 0 0 auto;
            flex: 0 0 auto;
            position: relative;
            box-sizing: border-box;
            width: 3px;
            height: 100%;
            border-left-width: 1px;
            border-left-style: solid;
            border-left-color: black;
            border-right-width: 1px;
            border-right-style: solid;
            border-right-color: black;
            cursor: ew-resize;
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            -khtml-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        .xui-resize-handle--x:before {
            content: "";
            position: absolute;
            z-index: 1;
            top: 50%;
            right: 100%;
            height: 18px;
            width: 2px;
            margin-top: -9px;
            border-left-color: black;
            border-left-width: 1px;
            border-left-style: solid;
        }

        .xui-resize-handle--x:after {
            content: "";
            position: absolute;
            z-index: 1;
            top: 50%;
            left: 100%;
            height: 18px;
            width: 2px;
            margin-top: -9px;
            border-right-color: black;
            border-right-width: 1px;
            border-right-style: solid;
        }
        
        .xui-scroller-left {
            direction: rtl;
        }

        .xui-scroller-left * {
            direction: ltr;
        }
        

        .xui-page>main {
            -webkit-order: 0;
            -ms-flex-order: 0;
            order: 0;
            -webkit-flex: 1 1 auto;
            -ms-flex: 1 1 auto;
            flex: 1 1 auto;
            -webkit-align-self: auto;
            -ms-flex-item-align: auto;
            align-self: auto;
            padding: 0px;
            position:relative;
        }

        .xui-page>main .hashframe-overlay {
            display:none;
            border: none;
            margin: 0px;
            height: 100%;
            width: 100%;
            background-color: red;
            position:absolute;
            top:0;
            left:0;
            opacity: 10%;
        }        
        
        .xui-page>main iframe {
            border: none;
            margin: 0px;
            height: 100%;
            width: 100%;
        }        
        
        .xui-sidebar {
            -webkit-order: 0;
            -ms-flex-order: 0;
            order: 0;
            -webkit-flex: 0 0 auto;
            -ms-flex: 0 0 auto;
            flex: 0 0 auto;
            -webkit-align-self: auto;
            -ms-flex-item-align: auto;
            align-self: auto;

            padding-left: 5px;
            overflow: hidden;
        }
        
        .xui-sidebar .xui-nav {
            width: 100%;
            min-width: 170px;
            height : calc(100vh - 100px);
            flex-wrap: nowrap;
            overflow: auto;
        }
        .xui-sidebar .xui-nav-header {
            width: 100%;
            min-width: 170px;
            flex-wrap: nowrap;
            overflow: auto;
        }

    </style>

    <script>
        window.console = window.console || function (t) { };
        
    </script>
</head>

<body>
    <div class="xui-page">
        <aside class="xui-sidebar bg-dark ps-2 d-flex flex-column align-items-center align-items-sm-start text-white justify-content-between">
            <a href="/" class="xui-nav-header d-flex align-items-center py-2 mb-md-0 me-md-auto text-white text-decoration-none">
                <span class="icon"><?=$o->header->icon??''?></span> 
                <span class="ms-1 fs-5 d-none d-sm-inline"><?=$o->header->label??''?></span>
            </a>
            <?php 
                ($nav_render__fn = function ($nav, $is_root = true) use(&$nav_render__fn){ ?>
                    <?php if($is_root): ?>
                    <ul class="xui-nav nav nav-pills flex-column mb-sm-auto mb-0 align-items-center align-items-sm-start" id="<?=$menu=uniqid('menu-')?>">
                    <?php else: ?>
                    <ul class="show nav flex-column ms-2">
                    <?php endif ?>
                    <?php foreach($nav as $k => $v): ?>
                        <?php if(\is_scalar($v)): ?>
                            <li><span class="section-label"><?=$v?></span></li>
                        <?php elseif(isset($v->inner)): ?>
                        <li>
                            <a href="#<?=$submenu=uniqid('menu-')?>" data-bs-toggle="collapse" class="nav-link px-0 align-middle text-white text-decoration-none">
                                <span class="icon"><?=$v->icon??''?></span> <span class="ms-1 d-none d-sm-inline"><?=$v->label??''?></span> 
                            </a>
                            <div class="collapse" id="<?=$submenu?>" data-bs-parent="#<?=$menu?>">
                                <?php ($nav_render__fn)($v->inner, false) ?>
                            </div>
                        </li>
                        <?php else: ?>
                        <li class="nav-item">
                            <a href="<?=($url = $v->url ?? null) ? "#{$url}": 'javascript:void(0)'?>" class="nav-link align-middle px-0 text-white text-decoration-none">
                                <span class="icon"><?=$v->icon??''?></span> <span class="ms-1 d-none d-sm-inline"><?=$v->label??''?></span> 
                            </a>
                        </li>
                        <?php endif ?>
                    <?php endforeach ?>
                    </ul><?php 
                })($o->page->nav);
            
                ($footer_rended__fn = function ($o) use(&$footer_rended__fn){?>
                    <div class="dropdown pb-2 dropup mt-3">
                        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false" style="width:100%">
                            <img src="<?=$o->footer->icon?>" alt="hugenerd" width="30" height="30" class="rounded-circle">
                            <span class="d-none d-sm-inline mx-1" style="max-width:120px; overflow:hidden"><?=$o->footer->label?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
                            <?php foreach($o->footer->nav as $k => $v): ?>
                                <?php if($v == '-'): ?>
                                    <li><hr class="dropdown-divider"></li>
                                <?php elseif(\is_scalar($v)): ?>
                                    <li><span class="section-label"><?=$v?></span></li>
                                <?php elseif(\is_object($v)): ?>
                                    <li><a class="dropdown-item" href="<?=($url = $v->url ?? null) ? "#{$url}": 'javascript:void(0)'?>"><?=$v->label??''?></a></li>
                                <?php else: ?>
                                <?php endif ?>
                            <?php endforeach ?>
                        </ul>
                    </div>
                <?php })($o);
            ?>
        </aside>
        <div class="xui-resize-handle--x" data-target="aside"></div>
        <main>
            <div class="hashframe-overlay"></div>
            <iframe id="id-hashframe-iframe" src="" frameborder="0"></iframe>
        </main>
    </div>
    <script>
        
        xui.hashframe = {
            init(){
                window.addEventListener('hashchange', () => {
                    (xui.TRACE_2) && console.log('changed hash is ' + window.location.hash);
                    
                    if(window.location.hash != '#.'){
                        if (this.reloading) {
                            return;
                        }
                        
                        this.hash = window.location.hash;
                        this.url = this.hash.substring(1);
                        (xui.TRACE_2) && console.log({
                            action: 'loading window hash',
                            'xui.hashframe': this,
                        });
                        
                        console.log(this.url);
                        var url_path = this.url_path(this.url);
                        if(url_path == this.url_path(window.location)){
                            this.url = url_path+'?0=1';
                        }

                        this.before_loading();
                        sessionStorage.setItem('last_hash',this.hash);
                        document.getElementById('id-hashframe-iframe').src = this.url;
                        this.nav?.set_active();
                        this.readjust_hash();
                    }
                    
                }, false);
            },
            url_path(url){
                if(url instanceof Location){
                    return url.protocol + "//" + url.host + "/" + url.pathname;
                } else if(url instanceof URL) {
                    return url.protocol + "//" + url.hostname + "/" + url.pathname;
                } else if(url instanceof String) {
                    var i = url.indexOf('?');
                    if(i >= 0){
                        url.substring(0, i);    
                    } else {
                        return url;    
                    }
                }
            },
            before_loading() {
                xui.reload_spinner?.start();
                //! credits: https://stackoverflow.com/a/1309769/10753162
                var frame = document.getElementById('id-hashframe-iframe')
                var frameDoc = frame.contentDocument || frame.contentWindow.document;
                frameDoc.removeChild(frameDoc.documentElement);
            },
            readjust_hash() {
                //* credits: https://stackoverflow.com/questions/1397329/how-to-remove-the-hash-from-window-location-url-with-javascript-without-page-r/5298684#5298684
                if (window.history.pushState) {
                    // window.history.pushState('', '/', window.location.pathname) //* this causes issues
                    window.history.pushState('', '/', window.location)
                    window.location.hash = '#.';
                } else {
                    window.location.hash = '#.';
                }
            },            
        };
        
        xui.resizer = {

            tracking: false,
            startWidth: null,
            startCursorScreenX: null,
            handleWidth: 5,
            resizeTarget: null,
            parentElement: null,
            maxWidth: null,
            init(){
                var target$ = $('.xui-sidebar');
                target$.outerWidth(target$.outerWidth());
                
                $(document.body).on('mousedown', '.xui-resize-handle--x', null, event => {
                    if (event.button !== 0) {
                        return;
                    }

                    event.preventDefault();
                    event.stopPropagation();

                    const handleElement = event.currentTarget;

                    if (!handleElement.parentElement) {
                        console.error(new Error("Parent element not found."));
                        return;
                    }

                    // Use the target selector on the handle to get the resize target.
                    const targetSelector = handleElement.getAttribute('data-target');
                    const targetElement = this.selectTarget(handleElement.parentElement, targetSelector);

                    if (!targetElement) {
                        console.error(new Error("Resize target element not found."));
                        return;
                    }
                    console.log(targetElement);
                    this.startWidth = $(targetElement).outerWidth();
                    this.startCursorScreenX = event.screenX;
                    this.resizeTarget = targetElement;
                    this.parentElement = handleElement.parentElement;
                    this.maxWidth = $(handleElement.parentElement).innerWidth() - this.handleWidth;
                    this.tracking = true;
                    $('.hashframe-overlay').show();
                    console.log('tracking started');
                });
                
                $(window).on('mousemove', null, null, _.debounce(event => {
                    if (this.tracking) {
                        const cursorScreenXDelta = event.screenX - this.startCursorScreenX;
                        const newWidth = Math.min(this.startWidth + cursorScreenXDelta, this.maxWidth);

                        $(this.resizeTarget).outerWidth(newWidth);
                    }
                }, 1));

                $(window).on('mouseup', null, null, event => {
                    if (this.tracking) {
                        this.tracking = false;
                        $('.hashframe-overlay').hide();
                        console.log('tracking stopped');
                    }
                });
            },
            selectTarget(fromElement, selector){
                if (!(fromElement instanceof HTMLElement)) {
                    return null;
                }
                return fromElement.querySelector(selector);
            },
            
            
        }
        
        $(document).ready(function(){
            xui.hashframe.init();
            xui.resizer.init(); 
        });
    </script>


</body>

</html>
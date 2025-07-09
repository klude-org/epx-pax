<?php 
    $o = (object)[ 'page' => (object) [], 'window' => (object) [], 'header' => (object) [], 'footer' => (object) [], ];
    $o->window->title = 'Menu';
    $o->window->icon = "";
    $o->header->label = 'Menu';
    $o->header->icon = "<i class=\"bi bi-grid-fill\"></i>";
    $o->footer->label = "brian.pinto@cybernetworks.com.au";
    $o->footer->icon = "https://github.com/mdo.png";
    // $o->footer->nav = \json_decode(\file_get_contents(\_\path_here('footer_nav.json')));
    // $o->page->nav = \json_decode(\file_get_contents(\_\path_here('nav.json')));
?>
<?php 
    // $o = (object)[];
    // $o->sidebar = (function(){ 
    //     if($file = $this->file('console/sidebar-$.json')){
    //         return $file->json->read();
    //     } else {
    //         return (object)['nav' => $this->panel->nav_tree('panel')];
    //     }
    // })();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bootstrap Sidebar Toggle</title>
    <script src="//code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="//www.w3schools.com/w3css/4/w3.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>    
    <style>
        body,
        html {
            height: 100%;
            margin: 0;
            overflow: hidden;
        }

        .xui-container {
            display: flex;
            height: 100%;
            width: 100%;
        }

        .xui-sidebar {
            width: 150px;
            transition: all 0.3s ease;
            background-color: #f8f9fa;
            position: relative;
        }

        .xui-sidebar.collapsed {
            display: none;
        }

        .xui-main {
            flex: 1;
            overflow: hidden;
            position: relative;
        }

        .xui-toggle-tab {
            position: absolute;
            top: 50%;
            left: 0;
            transform: translateY(-50%);
            width: 14px;
            height: 50px;
            background-color: #6c757d;
            cursor: pointer;
            z-index: 1000;
            opacity: 0.5;
            transition: opacity 0.3s;
            clip-path: polygon(0 0, 100% 15%, 100% 85%, 0% 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .xui-toggle-tab:hover {
            opacity: 1;
        }

        .xui-toggle-tab i {
            color: white;
            font-size: 12px;
        }

        iframe {
            width: 100%;
            height: 100%;
            border: none;
        }

        .xui-loader-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(255, 255, 255, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2000;
            display: none;
        }
    </style>
</head>

<body>
    <div class="xui-toggle-tab" id="sidebarToggle" onclick="toggleSidebar()">
        <i id="toggleIcon" class="bi bi-chevron-left"></i>
    </div>
    <div class="xui-container">
        <div class="xui-sidebar p-3" id="leftSidebar">
            <?php include 'sidebar_a/-v.php' ?>
        </div>
        <div class="xui-main">
            <div class="xui-loader-overlay" id="frameLoader">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            <iframe id="contentFrame"></iframe>
        </div>
    </div>
    <script>
        const sidebar = document.getElementById('leftSidebar');
        const toggleTab = document.getElementById('sidebarToggle');
        const toggleIcon = document.getElementById('toggleIcon');
        const contentFrame = document.getElementById('contentFrame');
        const frameLoader = document.getElementById('frameLoader');

        function toggleSidebar() {
            sidebar.classList.toggle('collapsed');
            toggleIcon.classList.toggle('bi-chevron-left');
            toggleIcon.classList.toggle('bi-chevron-right');
        }

        contentFrame.addEventListener('load', () => {
            frameLoader.style.display = 'none';
            try {
                const url = contentFrame.contentWindow.location.pathname;
                highlightActiveTab(url);
            } catch (e) {
                console.log(e);
                // ignore cross-origin access errors
            }
        });

        window.addEventListener('message', (event) => {
            if (event.data === 'start-loading') {
                console.log(event);
                frameLoader.style.display = 'flex';
            } else if (event.data === 'stop-loading') {
                console.log(event);
                frameLoader.style.display = 'none';
            }
        });

        // contentFrame.addEventListener('unload', () => {
        //   frameLoader.style.display = 'flex';
        // });

        // contentFrame.addEventListener('beforeunload', () => {
        //   frameLoader.style.display = 'flex';
        // });


        contentFrame.addEventListener('loadstart', () => {
            frameLoader.style.display = 'flex';
        });

        const observer = new MutationObserver(() => {
            frameLoader.style.display = 'flex';
        });

        observer.observe(contentFrame, { attributes: true, attributeFilter: ['src'] });
    </script>
</body>

</html>
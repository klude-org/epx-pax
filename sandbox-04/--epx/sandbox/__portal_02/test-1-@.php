<!DOCTYPE html>
<html lang="en">

<head>
    <!-- <meta charset="UTF-8"> -->
    <!--<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">-->
    <!-- <meta name="description" content=""> -->
    <!-- <meta name="author" content="Brian Pinto"> -->
    <!-- <meta name="generator" content="Hugo 0.84.0"> -->
    <!-- <base href="https://website.com/path/"> -->
    <!-- <link rel="canonical" href="https://website.com/"> -->

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body,
        html {
            height: 100%;
            margin: 0;
            overflow: hidden;
        }


        /*############################################################################*/
        /* scrollbar */
        ::-webkit-scrollbar {
            width: 5px;
        }

        ::-webkit-scrollbar-track {
            background: #eee;
        }

        ::-webkit-scrollbar-thumb {
            background: #9f9f9f;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #5f5f5f;
        }

        .xui-page {
            height: 100%;
            overflow: hidden;
            background-color: white;
        }

        /*############################################################################*/

        .xui-container {
            display: flex;
            height: 100%;
            width: 100%;
        }

        .xui-sidebar {
            /* width: 150px; */
            transition: all 0.3s ease;
            background-color: #f8f9fa;
            position: relative;

            padding-left: 5px;
            overflow: hidden;
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

        .xui-sidebar .xui-nav {
            width: 100%;
            min-width: 170px;
            height: calc(100vh - 80px);
            flex-wrap: nowrap;
            overflow: auto;
        }

        .active-highlight {
            font-weight: bold;
        }
        
        .bi-chevron-right {
            display: inline-block;
            transition: transform 0.3s ease;
        }        
        
        .rotate {
            /* display: inline-block; */
            transform: rotate(90deg);
            /* transition: transform 0.3s ease; */
        }        
        
    </style>
</head>

<body>
    <div class="xui-toggle-tab" id="sidebarToggle" onclick="toggleSidebar()">
        <i id="toggleIcon" class="bi bi-chevron-left"></i>
    </div>
    <div class="xui-container">
        <div class="xui-sidebar p-1" id="leftSidebar">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <a href="/" class="xui-nav-header d-flex align-items-center mb-md-0 me-md-auto text-decoration-none">
                    <span class="icon"><i class="bi bi-grid-fill"></i></span>
                    <span class="ms-1 fs-5 d-none d-sm-inline">Menu</span>
                </a>
            </div>
            <ul class="xui-nav nav nav-pills flex-column mb-sm-auto mb-0 align-items-center align-items-sm-start" id="sidebarMenu">
                <li class="nav-item w-100">
                    <a href="javascript:void(0)" class="nav-link align-middle text-decoration-none" data-path="home" onclick="navigate('home')">
                        <span class="icon"><i class="bi-house"></i></span> <span class="ms-1 d-none d-sm-inline">Home</span>
                    </a>
                </li>
                <li class="nav-item w-100">
                    <a href="javascript:void(0)" class="nav-link align-middle text-decoration-none" data-path="dashboard" onclick="navigate('dashboard')">
                        <span class="icon"><i class="bi-speedometer2"></i></span> <span class="ms-1 d-none d-sm-inline">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="#menu-686e2557bf2ee" data-bs-toggle="collapse" class="nav-link align-middle text-decoration-none" data-path="office">
                        <span class="icon"><i class="bi-people"></i></span> <span class="ms-1 d-none d-sm-inline">Office <i class="bi bi-chevron-right"></i></span>
                    </a>
                    <div class="collapse" id="menu-686e2557bf2ee" data-bs-parent="#sidebarMenu">
                        <ul class="nav flex-column ms-2">
                            <li class="nav-item w-100">
                                <a href="javascript:void(0)" class="nav-link align-middle text-decoration-none" data-path="office/documents" onclick="navigate('office/documents')">
                                    <span class="icon">&#128455;</span> <span class="ms-1 d-none d-sm-inline">Documents</span>
                                </a>
                            </li>
                            <li class="nav-item w-100">
                                <a href="javascript:void(0)" class="nav-link align-middle text-decoration-none" data-path="office/memos" onclick="navigate('office/memos')">
                                    <span class="icon">&#128455;</span> <span class="ms-1 d-none d-sm-inline">Memo</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item w-100">
                    <a href="javascript:void(0)" class="nav-link align-middle text-decoration-none" data-path="noticeboard" onclick="navigate('noticeboard')">
                        <span class="icon">&#128455;</span> <span class="ms-1 d-none d-sm-inline">Notice Board</span>
                    </a>
                </li>
                <li>
                    <a href="#menu-686e2557bf307" data-bs-toggle="collapse" class="nav-link align-middle text-decoration-none" data-path="employee">
                        <span class="icon">&#128455;</span> <span class="ms-1 d-none d-sm-inline">Employee <i class="bi bi-chevron-right"></i></span>
                    </a>
                    <div class="collapse" id="menu-686e2557bf307" data-bs-parent="#sidebarMenu">
                        <ul class="nav flex-column ms-2">
                            <li class="nav-item w-100">
                                <a href="javascript:void(0)" class="nav-link align-middle text-decoration-none" data-path="employee/user" onclick="navigate('employee/user')">
                                    <span class="icon">&#128455;</span> <span class="ms-1 d-none d-sm-inline">User</span>
                                </a>
                            </li>
                            <li class="nav-item w-100">
                                <a href="javascript:void(0)" class="nav-link align-middle text-decoration-none" data-path="employee/profile" onclick="navigate('employee/profile')">
                                    <span class="icon">&#128455;</span> <span class="ms-1 d-none d-sm-inline">Profile</span>
                                </a>
                            </li>
                            <li class="nav-item w-100">
                                <a href="javascript:void(0)" class="nav-link align-middle text-decoration-none" data-path="employee/documents" onclick="navigate('employee/documents')">
                                    <span class="icon">&#128455;</span> <span class="ms-1 d-none d-sm-inline">Documents</span>
                                </a>
                            </li>
                            <li class="nav-item w-100">
                                <a href="javascript:void(0)" class="nav-link align-middle text-decoration-none" data-path="employee/payslips" onclick="navigate('employee/payslips')">
                                    <span class="icon">&#128455;</span> <span class="ms-1 d-none d-sm-inline">Payslips</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>
            <div class="dropdown dropup pt-2">
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="https://github.com/mdo.png" alt="hugenerd" width="30" height="30" class="rounded-circle">
                    <span class="flex-fill d-none d-sm-inline mx-1" style="max-width:120px; overflow:hidden">brian.pinto@cybernetworks.com.au</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
                    <li><a class="dropdown-item" href="javascript:void(0)" data-path="--user/new_project" onclick="navigate('--user/new_project')">New Project ...</a></li>
                    <li><a class="dropdown-item" href="javascript:void(0)" data-path="--user/settings" onclick="navigate('--user/settings')">Settings</a></li>
                    <li><a class="dropdown-item" href="javascript:void(0)" data-path="--user/profile" onclick="navigate('--user/profile')">Profile</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item" href="javascript:void(0)" data-path=".?--logout" onclick="navigate('.?--logout')">Sign Out</a></li>
                </ul>
            </div>
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
        const sidebarMenu = document.getElementById('sidebarMenu');
        const frameLoader = document.getElementById('frameLoader');

        function toggleSidebar() {
            sidebar.classList.toggle('collapsed');
            toggleIcon.classList.toggle('bi-chevron-left');
            toggleIcon.classList.toggle('bi-chevron-right');
        }

        const CTLR_URL = 'http://fw.local/web-github/klude-org/epx-pax/sandbox-04/--panel-02'
        const BASE_URL = 'http://fw.local/web-github/klude-org/epx-pax/sandbox-04/--panel-02'
        const SITE_URL = 'http://fw.local/web-github/klude-org/epx-pax/sandbox-04';
        const ROOT_URL = 'http://fw.local';
        console.log({ CTLR_URL, BASE_URL, SITE_URL, ROOT_URL });
        function navigateWithoutReferrer(url) {
            const a = document.createElement('a');
            a.href = url;
            a.rel = 'noreferrer';
            a.target = '_self'; // same as window.location
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }

        function navigate(page) {
            if (page.startsWith('--')) {
                // Use ROOT_URL and remove the '--' prefix
                contentFrame.src = SITE_URL + '/' + page.slice(2);
            } else if (
                page.startsWith('?')
            ) {
                contentFrame.src = CTLR_URL + page;
            } else if (
                page.startsWith('//') ||
                page.startsWith('https://') ||
                page.startsWith('http://')
            ) {
                // Navigate the main window
                navigateWithoutReferrer(page);
            } else if (
                page.startsWith('/')
            ) {
                // Navigate the main window
                navigateWithoutReferrer(page);
            } else if (
                page.startsWith('.')
            ) {
                // Navigate the main window
                navigateWithoutReferrer(CTLR_URL + page.substring(1));
            } else {
                // Default behavior with baseUrl
                const baseUrl = window.location.origin + window.location.pathname;
                contentFrame.src = baseUrl.replace(/\/$/, '') + '/' + page;
            }
        }

        function highlightActiveTab(url) {
            const links = sidebarMenu.querySelectorAll('a[data-path]');
            links.forEach(link => {
                const path = link.getAttribute('data-path');
                var path_url = CTLR_URL + '/' + path;
                var frame_url = ROOT_URL + url;
                console.log({ frame_url, path_url, path });
                if (frame_url == path_url) {
                    //link.classList.add('active');
                    link.classList.add('active-highlight');
                } else if (frame_url.startsWith(path_url)) {
                    link.classList.add('active-highlight');
                } else {
                    link.classList.remove('active');
                    link.classList.remove('active-highlight');
                }
            });
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
    
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const collapses = document.querySelectorAll('[data-bs-toggle="collapse"]');
            collapses.forEach(toggle => {
                const chevron = toggle.querySelector('.bi-chevron-right');
                const targetId = toggle.getAttribute('href');
                const collapseEl = document.querySelector(targetId);

                if (!collapseEl || !chevron) return;

                collapseEl.addEventListener('show.bs.collapse', function () {
                    chevron.classList.add('rotate');
                });

                collapseEl.addEventListener('hide.bs.collapse', function () {
                    chevron.classList.remove('rotate');
                });
            });
        });
    </script>   
 
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>console.log({ server: ["Loading Cache Config: C:\/fw\/web-github\/klude-org\/epx-pax\/sandbox-04\/.local\/.config-cache-web.php", "TSP: C:\/fw\/web-github\/klude-org\/epx-pax\/plugins\/epx__250709_02_abaca__pax__klude_org__github;C:\/fw\/web-github\/klude-org\/epx-pax\/sandbox-04\/--epx\/sandbox;C:\/fw\/web-github\/klude-org\/epx-pax\/sandbox-04\/--epx;C:\/fw\/web-github\/klude-org\/epx-pax\/sandbox-04\/--epx\/.local-plugins;C:\/xampp\/8.2.0-0-VS16\/php__xdbg\/PEAR", "Instance: _\\env\\intfc\\web\\panel", "Node Not Found: 'vars'"] })</script>
</body>

</html>
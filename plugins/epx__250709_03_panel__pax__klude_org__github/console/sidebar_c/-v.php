<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Menu</h5>
</div>
<ul class="nav nav-pills flex-column" id="sidebarMenu">
    <li class="nav-item"><a class="nav-link" href="#" data-path="customer" onclick="navigate('customer')">Customer</a></li>
    <li class="nav-item"><a class="nav-link" href="#" data-path="orders" onclick="navigate('orders')">Orders</a></li>
    <li class="nav-item"><a class="nav-link" href="#" data-path="products" onclick="navigate('products')">Products</a></li>
</ul>
<script>
    const sidebarMenu = document.getElementById('sidebarMenu');

    function navigate(page) {
        const baseUrl = window.location.origin + window.location.pathname;
        contentFrame.src = baseUrl + '/' + page;
    }

    function highlightActiveTab(url) {
        const links = sidebarMenu.querySelectorAll('a[data-path]');
        links.forEach(link => {
            const path = link.getAttribute('data-path');
            if (url.includes('/' + path)) {
                link.classList.add('active');
            } else {
                link.classList.remove('active');
            }
        });
    }
</script>
<ul class="navbar-nav bg-white sidebar sidebar-light accordion" id="accordionSidebar">

    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
        <span class="admin_brand admin_brand_sidebar">CADIPEL</span>
    </a>

    <hr class="sidebar-divider my-0">
    <hr class="sidebar-divider">

    <li class="nav-item <?= ($page === 'prompt' || $page === '') ? 'active' : '' ?>">
        <a class="nav-link" href="index.php?page=prompt">
            <svg class="nav-link-icon" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path fill="currentColor" d="M2 2.75C2 2.336 2.336 2 2.75 2h10.5c.414 0 .75.336.75.75v7.5a.75.75 0 0 1-.75.75H9.06l-2.56 2.56a.75.75 0 0 1-1.28-.53V11H2.75a.75.75 0 0 1-.75-.75v-7.5Z"/>
                <path fill="currentColor" d="M4.5 5.25h7v1H4.5v-1Zm0 2.5h5v1h-5v-1Z" opacity=".55"/>
            </svg>
            <span>Prompt IA</span>
        </a>
    </li>

</ul>

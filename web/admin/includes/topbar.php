                <nav class="navbar navbar-expand navbar-light bg-primary topbar mb-4 static-top shadow">

                    <button type="button" class="cadipel_nav_toggle" id="sidebarToggleTop" aria-label="Menú">
                        <img src="img/icons/menu_icon.svg" class="cadipel_nav_toggle_icon" alt="" aria-hidden="true">
                    </button>

                    <ul class="navbar-nav ml-auto cadipel_topbar_actions">

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <li class="nav-item">
                            <span class="nav-link cadipel_topbar_user">
                                <span class="mr-2 d-none d-lg-inline text-white-600 small"><?= htmlspecialchars($_SESSION['cadipel_admin'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                                <img class="img-profile rounded-circle" src="img/undraw_profile.svg" alt="">
                            </span>
                        </li>

                        <li class="nav-item">
                            <a class="cadipel_logout_btn" href="index.php?page=logout" aria-label="Salir">
                                <img src="img/icons/logout_icon.svg" class="cadipel_logout_icon" alt="" aria-hidden="true">
                                <span class="cadipel_logout_label">Salir</span>
                            </a>
                        </li>

                    </ul>

                </nav>

(function () {
    var sidebarBtn = document.getElementById('sidebarToggleTop');
    if (sidebarBtn) {
        sidebarBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            document.body.classList.toggle('sidebar_mobile_open');
        });
    }

    document.addEventListener('click', function (e) {
        if (document.body.classList.contains('sidebar_mobile_open')
            && !e.target.closest('#accordionSidebar')) {
            document.body.classList.remove('sidebar_mobile_open');
        }
    });
})();

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

<script>
  function alert(type, msg) {
    let bs_class = (type == 'success') ? 'alert-success' : 'alert-danger';
    let element = document.createElement('div');
    element.innerHTML = `
    <div class="alert ${bs_class} alert-dismissible fade show custom-alert" role="alert">
    <strong class="me-3">${msg}</strong>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>`;
  document.body.append(element);
  setTimeout(remAlert, 2000);
  }

  function remAlert(){
    document.getElementsByClassName('custom-alert')[0].remove();
  }

  // Active state is now handled cleanly by PHP in header.php


function toggleSidebar() {
    const sidebar = document.getElementById('dashboard-menu');
    const mainContent = document.getElementById('main-content');
    
    sidebar.classList.toggle('collapsed');
    if(mainContent) mainContent.classList.toggle('expanded');

    if (sidebar.classList.contains('collapsed')) {
        localStorage.setItem('sidebarStatus', 'collapsed');
    } else {
        localStorage.setItem('sidebarStatus', 'expanded');
    }
}

function checkSidebarState() {
    const sidebar = document.getElementById('dashboard-menu');
    const mainContent = document.getElementById('main-content');
    const status = localStorage.getItem('sidebarStatus');

    if (status === 'collapsed') {
        if(sidebar) sidebar.classList.add('collapsed');
        if(mainContent) mainContent.classList.add('expanded');
    }
}

checkSidebarState();

</script>
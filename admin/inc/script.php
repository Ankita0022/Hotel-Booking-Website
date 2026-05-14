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

  // Update setActive to handle the active state correctly
function setActive(){
    let navbar = document.getElementById('dashboard-menu');
    let a_tags = navbar.getElementsByTagName('a');

    for(let i=0; i<a_tags.length; i++){
        let file = a_tags[i].href.split('/').pop();
        let file_name = file.split('.')[0];

        if(document.location.href.indexOf(file_name) >= 0){
            a_tags[i].classList.add('active');
            a_tags[i].classList.add('custom-bg'); // Using your existing teal color
        }
    }
}
setActive();


function toggleSidebar() {
    const sidebar = document.getElementById('dashboard-menu');
    const mainContent = document.getElementById('main-content');
    
    sidebar.classList.toggle('collapsed');
    mainContent.classList.toggle('expanded');

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
        sidebar.classList.add('collapsed');
        mainContent.classList.add('expanded');
    }
}

checkSidebarState();

</script>
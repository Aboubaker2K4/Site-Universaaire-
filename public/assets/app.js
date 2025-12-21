document.addEventListener('click', function(e){
  if(e.target.matches('.burger-toggle')){
    const menu = document.querySelector('.burger-menu');
    if(menu) menu.classList.toggle('open');
  }
});

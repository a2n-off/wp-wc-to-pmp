console.log('js loaded');

const btns = document.getElementsByClassName('pmp-sender');

for (var i=0; i<btns.length; i++) {
 btns[i].addEventListener('click', () => {
   const order_id = btns[i].dataset.pmp;
   console.log(order_id);
 })
}

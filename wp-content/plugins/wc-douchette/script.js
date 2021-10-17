console.log('wcdouchette js loaded');

// get all the pmp buttons
const elems = document.getElementsByClassName("pmp-sender");

// init xhr function
function sendToPmp(elem) {
  const host = window.location.origin;
  const id = elem.dataset.pmp;
  const url = host + '/wp-json/wcdouchette/v1/send/' + id;
  console.log(url);
  const xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function() {
    // reload the page after the update
    // give the possibilty to update the data into the table
    if (xhr.readyState == XMLHttpRequest.DONE) window.location.reload(false);
  }
  xhr.open("POST", url, true);
  xhr.send();
}

// add event on each btn
for (let i=0; i<elems.length; i++) {
  console.log(elems[i]);
  elems[i].addEventListener('click', () => sendToPmp(elems[i]));
}

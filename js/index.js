var textArea =  document.getElementById('textareaID')
var id = document.getElementById('IDer')
var commentSec = document.getElementById('commentSec');
var textareaError = document.getElementById('container0');
var deleteID = document.getElementById('deleteIDer');
let count = 5;

function eraseText() {
  textArea.value = "";
}




function trimfield(str) 
{ 
    return str.replace(/^\s+|\s+$/g,''); 
}


// AJAX FIELDS
var x = document.getElementById('commentform')
if(x != null){
x.addEventListener('submit', onSubmit);

function onSubmit(e) {
  e.preventDefault()
  const formData = new FormData(this)
  const xhr = new XMLHttpRequest()
  xhr.open('post', 'display_team.php?id=' + id.value);

  xhr.addEventListener('load', function () { 
    if(trimfield(textArea.value) == '')
    {
      textareaError.style.display ="block";
      }else{
    var temp = document.createElement('div');
    temp.innerHTML = this.responseText;
   // console.log(temp.querySelector('#commentSec').innerHTML);
    commentSec.innerHTML = temp.querySelector('#commentSec').innerHTML;
    textareaError.style.display ="none";
    eraseText()
    }
   })
  xhr.send(formData);

}
}

function like(placeholder) {

  var targetUrl = placeholder.getAttribute('rel');
  //console.log(targetUrl);
  let xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        var temp = document.createElement('div');
        temp.innerHTML = this.responseText;
        commentSec.innerHTML = temp.querySelector('#commentSec').innerHTML;
      }
  };
  xhr.open("GET", targetUrl, true);
  xhr.send();
}




const matchesBody = document.querySelector("#matches > tbody");
const showmorebtn = document.getElementById("show-more");
const showlessbtn = document.getElementById("show-less");
const lengthTable = document.getElementById("table-length");

if(showmorebtn != null){
showmorebtn.addEventListener("click", ExtendMatches);
}
if(showlessbtn != null){
  showlessbtn.addEventListener("click", ReduceMatches);
  }
function ExtendMatches(e) {
  e.preventDefault()
 // console.log("Hello");
  let xhr = new XMLHttpRequest()
  count=(lengthTable.value < count + 5)?lengthTable.value: count + 5;

  xhr.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      var temp = document.createElement('div');
      temp.innerHTML = this.responseText;
    //  console.log(this.responseText);
      matchesBody.innerHTML = temp.querySelector('#matches > tbody').innerHTML;
      
    }
};
   xhr.open("GET", 'index.php?count=' + count, true);
  xhr.send();

}




function ReduceMatches(e) {
  e.preventDefault()
  //console.log("Hello");
  let xhr = new XMLHttpRequest()
  count= (count - 5) < 5? 5: count - 5 ;
  //console.log(count);
  xhr.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      var temp = document.createElement('div');
      temp.innerHTML = this.responseText;
      //console.log(this.responseText);
      matchesBody.innerHTML = temp.querySelector('#matches > tbody').innerHTML;
      
    }
};
   xhr.open("GET", 'index.php?count=' + count, true);
  xhr.send();

}

var thisteamid = document.getElementById('favUser');
var checkbox = document.getElementById('fav-toggle');
var counter = document.getElementById('ppl-counter');

if(checkbox != null){
checkbox.addEventListener('change', function () {
  if (checkbox.checked) {
    let xhr = new XMLHttpRequest()
    xhr.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        var temp = document.createElement('div');
        temp.innerHTML = this.responseText;
        counter.innerHTML = temp.querySelector('#ppl-counter').innerHTML;
      }
  };
    xhr.open("GET", 'display_team.php?id=' + thisteamid.value + '&favID='+ thisteamid.value, true);
    xhr.send();
   // console.log('Checked');
 } else {
   // console.log('Not checked');
    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        var temp = document.createElement('div');
        temp.innerHTML = this.responseText;
        counter.innerHTML = temp.querySelector('#ppl-counter').innerHTML;
      }
  };
    xhr.open("GET", 'display_team.php?id=' + thisteamid.value + '&delfavID='+ thisteamid.value, true);
    xhr.send();
  }
});
}
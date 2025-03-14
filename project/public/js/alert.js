
 function displayMessage(responseMessage){
    const alert = document.getElementById("alert");
    const message = document.getElementById('message');
    alert.classList.remove('hidden');
    message.textContent = responseMessage;
    setTimeout(function(){ alert.classList.add('hidden')}, 3000)
}
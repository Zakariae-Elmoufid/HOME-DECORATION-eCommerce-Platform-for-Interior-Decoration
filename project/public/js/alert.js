
export default function displayMessage(responseMessage,url){
    const alert = document.getElementById("alert");
    const message = document.getElementById('message');
    alert.classList.remove('hidden');
    message.textContent = responseMessage;
    setTimeout(function(){ alert.classList.add('hidden')}, 5000)
    window.location.href = url; 
}
export default function displayMessage(responseMessage, url, type = 'success') {
    const alert = document.getElementById("alert");
    const message = document.getElementById("message");
    const icon = document.getElementById("message-icon");
    const title = document.getElementById("message-title");

    const overlay = document.createElement("div");
    overlay.id = "message-overlay";
    overlay.className = "fixed inset-0 bg-black bg-opacity-50 z-40";
    document.body.appendChild(overlay);

    if (type === 'success') {
        alert.classList.remove('border-red-500');
        alert.classList.add('border-green-500');
        title.textContent = 'Succès !';
        title.className = 'text-lg font-medium text-green-800';
        message.className = 'mt-1 text-sm text-green-700';
        icon.innerHTML = `
            <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>`;
    } else {
        alert.classList.remove('border-green-500');
        alert.classList.add('border-red-500');
        title.textContent = 'Erreur !';
        title.className = 'text-lg font-medium text-red-800';
        message.className = 'mt-1 text-sm text-red-700';
        icon.innerHTML = `
            <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-4h2v2h-2v-2zm0-8h2v6h-2V6z" clip-rule="evenodd"/>
            </svg>`;
    }

    alert.classList.remove("hidden");
    message.textContent = responseMessage;
    document.body.style.overflow = "hidden";

    setTimeout(function () {
        alert.classList.add("hidden");
        overlay.remove();
        document.body.style.overflow = "";
        if (url) {
            window.location.href = url;
        }
    }, 2000);
}

const loginForm = document.querySelector("#login-form");
const errorMessage = document.querySelector("#error-message");
const loginButton = document.querySelector("#login-button");

loginForm.addEventListener("submit", async (event) => {
    event.preventDefault();

    loginButton.disabled = true;
    loginButton.textContent = "Logging in...";

    const username = document.querySelector("#username").value;
    const password = document.querySelector("#password").value;

    try {
        const response = await fetch("/users/authenticate", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                username, password
            })
        });

        const data = await response.json();

        if (!response.ok) {
            errorMessage.textContent = data.message ??  "Login failed.";
            return;
        }

        console.log(`Logged in: ${data}`);

        window.location.href = "userdashboard.html";
    } catch (error) {
        console.error(error);
        errorMessage.textContent = "Unable to connect to the server.";
    } finally {
        loginButton.disabled = false;
        loginButton.textContent = "Login";
    }
});
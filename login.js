document.getElementById('loginForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    const errorMessage = document.getElementById('errorMessage');
    
    // Clear previous error
    errorMessage.classList.remove('show');
    errorMessage.textContent = '';
    
    try {
        const response = await fetch('login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `username=${encodeURIComponent(username)}&password=${encodeURIComponent(password)}`
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Store user info in sessionStorage
            sessionStorage.setItem('user_id', data.user_id);
            sessionStorage.setItem('username', data.username);
            
            // Redirect to dashboard
            window.location.href = 'dashboard.html';
        } else {
            // Show error message
            errorMessage.textContent = data.message || 'Invalid username or password';
            errorMessage.classList.add('show');
        }
    } catch (error) {
        console.error('Error:', error);
        errorMessage.textContent = 'An error occurred. Please try again.';
        errorMessage.classList.add('show');
    }
});
// Check if user is logged in
const userId = sessionStorage.getItem('user_id');
const username = sessionStorage.getItem('username');

if (!userId) {
    window.location.href = 'index.html';
}

// Set welcome message
document.getElementById('welcomeUser').textContent = `Welcome, ${username}`;

// Logout functionality
document.getElementById('logoutBtn').addEventListener('click', function(e) {
    e.preventDefault();
    sessionStorage.clear();
    window.location.href = 'login.html';
});

// Load recordings on page load
loadRecordings();
loadStatistics();

// Refresh button
document.getElementById('refreshBtn').addEventListener('click', function() {
    loadRecordings();
    loadStatistics();
});

// Filter functionality
document.getElementById('filterBtn').addEventListener('click', function() {
    const filterDate = document.getElementById('filterDate').value;
    loadRecordings(filterDate);
});

document.getElementById('clearFilterBtn').addEventListener('click', function() {
    document.getElementById('filterDate').value = '';
    loadRecordings();
});

// Load recordings from server
async function loadRecordings(filterDate = '') {
    try {
        let url = `get_recordings.php?user_id=${userId}`;
        if (filterDate) {
            url += `&date=${filterDate}`;
        }
        
        const response = await fetch(url);
        const data = await response.json();
        
        const recordingsGrid = document.getElementById('recordingsGrid');
        recordingsGrid.innerHTML = '';
        
        if (data.success && data.recordings.length > 0) {
            data.recordings.forEach(recording => {
                const card = createRecordingCard(recording);
                recordingsGrid.appendChild(card);
            });
        } else {
            recordingsGrid.innerHTML = '<div class="no-recordings">No recordings found</div>';
        }
    } catch (error) {
        console.error('Error loading recordings:', error);
        document.getElementById('recordingsGrid').innerHTML = 
            '<div class="no-recordings">Error loading recordings</div>';
    }
}

// Load statistics
async function loadStatistics() {
    try {
        const response = await fetch(`get_statistics.php?user_id=${userId}`);
        const data = await response.json();
        
        if (data.success) {
            document.getElementById('totalIncidents').textContent = data.total_incidents;
            document.getElementById('todayIncidents').textContent = data.today_incidents;
            document.getElementById('storageUsed').textContent = data.storage_used + ' MB';
        }
    } catch (error) {
        console.error('Error loading statistics:', error);
    }
}

// Create recording card
function createRecordingCard(recording) {
    const card = document.createElement('div');
    card.className = 'recording-card';
    
    const date = new Date(recording.timestamp);
    const formattedDate = date.toLocaleDateString();
    const formattedTime = date.toLocaleTimeString();
    
    card.innerHTML = `
        <video preload="metadata">
            <source src="${recording.video_path}" type="video/mp4">
        </video>
        <div class="recording-info">
            <h3>Drowsiness Detected</h3>
            <p>📅 ${formattedDate}</p>
            <p>⏰ ${formattedTime}</p>
            <p>📹 Duration: 20 seconds</p>
        </div>
    `;
    
    card.addEventListener('click', function() {
        openVideoModal(recording);
    });
    
    return card;
}

// Modal functionality
const modal = document.getElementById('videoModal');
const closeBtn = document.getElementsByClassName('close')[0];

closeBtn.addEventListener('click', function() {
    modal.style.display = 'none';
    document.getElementById('modalVideo').pause();
});

window.addEventListener('click', function(event) {
    if (event.target == modal) {
        modal.style.display = 'none';
        document.getElementById('modalVideo').pause();
    }
});

function openVideoModal(recording) {
    const date = new Date(recording.timestamp);
    
    document.getElementById('modalTitle').textContent = 'Drowsiness Detection Recording';
    document.getElementById('modalVideo').src = recording.video_path;
    document.getElementById('modalDate').textContent = date.toLocaleDateString();
    document.getElementById('modalTime').textContent = date.toLocaleTimeString();
    
    modal.style.display = 'block';
}

// Download button
document.getElementById('downloadBtn').addEventListener('click', function() {
    const videoSrc = document.getElementById('modalVideo').src;
    const link = document.createElement('a');
    link.href = videoSrc;
    link.download = 'drowsiness_recording_' + new Date().getTime() + '.mp4';
    link.click();

});

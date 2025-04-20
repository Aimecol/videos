document.addEventListener('DOMContentLoaded', function() {
    // Video player elements
    const videoPlayer = document.getElementById('videoPlayer');
    const playPauseBtn = document.getElementById('playPauseBtn');
    const muteBtn = document.getElementById('muteBtn');
    const fullscreenBtn = document.getElementById('fullscreenBtn');
    const volumeSlider = document.getElementById('volumeSlider');
    const progressBar = document.getElementById('progressBar');
    const currentTimeDisplay = document.getElementById('currentTime');
    const durationDisplay = document.getElementById('duration');
    const bigPlayButton = document.getElementById('bigPlayButton');
    const completionMessage = document.getElementById('completionMessage');
    const replayBtn = document.getElementById('replayBtn');
    
    // Variables for tracking
    let isPlaying = false;
    let isMuted = false;
    let currentProgress = 0;
    let lastUpdateTime = 0;
    let updateInterval;
    let isCompleted = false;
    
    // Initialize video player
    function initPlayer() {
        // Set initial volume
        videoPlayer.volume = volumeSlider.value;
        
        // If user has watched this video before, set the current time
        if (videoData.lastPosition > 0) {
            videoPlayer.currentTime = videoData.lastPosition;
        }
        
        // Start update interval
        updateInterval = setInterval(updateProgress, 1000);
        
        // Format duration display once metadata is loaded
        videoPlayer.addEventListener('loadedmetadata', function() {
            durationDisplay.textContent = formatTime(videoPlayer.duration);
        });
        
        // Update time display during playback
        videoPlayer.addEventListener('timeupdate', function() {
            currentTimeDisplay.textContent = formatTime(videoPlayer.currentTime);
            
            // Update progress bar
            const progress = (videoPlayer.currentTime / videoPlayer.duration) * 100;
            progressBar.style.width = progress + '%';
            currentProgress = progress;
            
            // Check if video is completed (95% or more)
            if (progress >= 95 && !isCompleted && videoData.isLoggedIn) {
                isCompleted = true;
                updateWatchStatus(progress, videoPlayer.currentTime, true);
                showCompletionMessage();
            }
        });
        
        // Handle video end
        videoPlayer.addEventListener('ended', function() {
            isPlaying = false;
            playPauseBtn.innerHTML = '<i class="fas fa-play"></i>';
            bigPlayButton.style.display = 'flex';
            
            if (videoData.isLoggedIn) {
                updateWatchStatus(100, videoPlayer.duration, true);
                showCompletionMessage();
            }
        });
    }
    
    // Play/Pause functionality
    function togglePlayPause() {
        if (videoPlayer.paused) {
            videoPlayer.play();
            isPlaying = true;
            playPauseBtn.innerHTML = '<i class="fas fa-pause"></i>';
            bigPlayButton.style.display = 'none';
        } else {
            videoPlayer.pause();
            isPlaying = false;
            playPauseBtn.innerHTML = '<i class="fas fa-play"></i>';
            bigPlayButton.style.display = 'flex';
        }
    }
    
    // Mute/Unmute functionality
    function toggleMute() {
        if (videoPlayer.muted) {
            videoPlayer.muted = false;
            isMuted = false;
            muteBtn.innerHTML = '<i class="fas fa-volume-up"></i>';
            volumeSlider.value = videoPlayer.volume;
        } else {
            videoPlayer.muted = true;
            isMuted = true;
            muteBtn.innerHTML = '<i class="fas fa-volume-mute"></i>';
        }
    }
    
    // Fullscreen functionality
    function toggleFullscreen() {
        if (!document.fullscreenElement) {
            if (videoPlayer.requestFullscreen) {
                videoPlayer.requestFullscreen();
            } else if (videoPlayer.webkitRequestFullscreen) {
                videoPlayer.webkitRequestFullscreen();
            } else if (videoPlayer.msRequestFullscreen) {
                videoPlayer.msRequestFullscreen();
            }
            fullscreenBtn.innerHTML = '<i class="fas fa-compress"></i>';
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.webkitExitFullscreen) {
                document.webkitExitFullscreen();
            } else if (document.msExitFullscreen) {
                document.msExitFullscreen();
            }
            fullscreenBtn.innerHTML = '<i class="fas fa-expand"></i>';
        }
    }
    
    // Format time in seconds to MM:SS format
    function formatTime(seconds) {
        const minutes = Math.floor(seconds / 60);
        seconds = Math.floor(seconds % 60);
        return minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
    }
    
    // Update watch progress in the database
    function updateProgress() {
        if (!videoData.isLoggedIn || isCompleted) return;
        
        const currentTime = Math.floor(Date.now() / 1000);
        
        // Update every 10 seconds or when the video is paused
        if (currentTime - lastUpdateTime >= 10 || !isPlaying) {
            updateWatchStatus(currentProgress, videoPlayer.currentTime, false);
            lastUpdateTime = currentTime;
        }
    }
    
    // Send watch status to the server
    function updateWatchStatus(percentage, position, completed) {
        if (!videoData.isLoggedIn) return;
        
        fetch(videoData.apiUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                videoId: videoData.id,
                percentage: Math.round(percentage),
                position: Math.round(position),
                completed: completed
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && completed) {
                console.log('Video marked as completed');
            }
        })
        .catch(error => {
            console.error('Error updating watch status:', error);
        });
    }
    
    // Show completion message
    function showCompletionMessage() {
        completionMessage.classList.add('active');
    }
    
    // Event listeners
    playPauseBtn.addEventListener('click', togglePlayPause);
    bigPlayButton.addEventListener('click', togglePlayPause);
    videoPlayer.addEventListener('click', togglePlayPause);
    
    muteBtn.addEventListener('click', toggleMute);
    
    volumeSlider.addEventListener('input', function() {
        videoPlayer.volume = this.value;
        
        if (this.value > 0 && videoPlayer.muted) {
            videoPlayer.muted = false;
            isMuted = false;
            muteBtn.innerHTML = '<i class="fas fa-volume-up"></i>';
        } else if (this.value == 0) {
            videoPlayer.muted = true;
            isMuted = true;
            muteBtn.innerHTML = '<i class="fas fa-volume-mute"></i>';
        }
    });
    
    fullscreenBtn.addEventListener('click', toggleFullscreen);
    
    // Progress bar click to seek
    const progressContainer = document.querySelector('.progress-container');
    progressContainer.addEventListener('click', function(e) {
        const rect = this.getBoundingClientRect();
        const pos = (e.clientX - rect.left) / rect.width;
        videoPlayer.currentTime = pos * videoPlayer.duration;
        
        // Update progress immediately
        if (videoData.isLoggedIn) {
            const progress = (videoPlayer.currentTime / videoPlayer.duration) * 100;
            updateWatchStatus(progress, videoPlayer.currentTime, false);
        }
    });
    
    // Replay button
    replayBtn.addEventListener('click', function() {
        completionMessage.classList.remove('active');
        videoPlayer.currentTime = 0;
        togglePlayPause();
    });
    
    // Cleanup on page unload
    window.addEventListener('beforeunload', function() {
        if (videoData.isLoggedIn && !isCompleted) {
            updateWatchStatus(currentProgress, videoPlayer.currentTime, false);
        }
        
        clearInterval(updateInterval);
    });
    
    // Initialize player
    initPlayer();
});
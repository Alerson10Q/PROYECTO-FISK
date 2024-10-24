const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('success')) {
            const notification = document.getElementById('notification');
            notification.style.display = 'block';
            
            setTimeout(() => {
                notification.style.display = 'none';
            }, 3000);
        }
/**
 * Generic Service Queue Management JavaScript
 * 
 * Works with any service type (OPD Lab, Customer Service, Order Pickup, etc.)
 * Replaces hardcoded OPD Lab JavaScript logic.
 */

// Service configuration (set from Blade template)
const serviceConfig = window.serviceConfig || {};
let secondScreen = null;

// Get service labels (from database)
function getServiceLabels() {
    if (!serviceConfig.labels) return {};
    
    const labels = {};
    serviceConfig.labels.forEach(label => {
        labels[label.id] = {
            id: label.id,
            label: label.label,
            color: label.color,
            translations: label.translations || {}
        };
    });
    return labels;
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    const callBtn = document.getElementById('callBtn');
    const openSecondScreenBtn = document.getElementById('openSecondScreen');
    
    if (callBtn) {
        callBtn.addEventListener('click', handleCall);
    }
    
    if (openSecondScreenBtn) {
        openSecondScreenBtn.addEventListener('click', handleOpenSecondScreen);
    }
});

/**
 * Handle CALL button click
 */
function handleCall() {
    const labels = getServiceLabels();
    const labelSelect = document.getElementById('labelSelect');
    const selectedLabelId = labelSelect ? labelSelect.value : null;
    
    if (labelSelect && selectedLabelId && labels[selectedLabelId]) {
        const labelInfo = labels[selectedLabelId];
        const selectedOption = labelSelect.options[labelSelect.selectedIndex];
        
        if (serviceConfig.serviceType === 'range') {
            // Range-based calling
            const start = parseInt(document.getElementById('startValue').value);
            const end = parseInt(document.getElementById('endValue').value);
            
            if (isNaN(start) || isNaN(end) || start > end) {
                alert("Please enter valid start and end numbers");
                return;
            }
            
            displayTokensRange(start, end, labelInfo);
        } else {
            // Sequential calling
            const number = parseInt(document.getElementById('numberValue').value);
            
            if (isNaN(number) || number < 1) {
                alert("Please enter a valid number");
                return;
            }
            
            displayTokenSequential(number, labelInfo);
        }
    } else if (labelSelect) {
        alert("Please select an option");
    } else {
        // No labels configured, use default display
        if (serviceConfig.serviceType === 'range') {
            const start = parseInt(document.getElementById('startValue').value);
            const end = parseInt(document.getElementById('endValue').value);
            
            if (isNaN(start) || isNaN(end) || start > end) {
                alert("Please enter valid start and end numbers");
                return;
            }
            
            displayTokensRange(start, end, { label: serviceConfig.serviceName, color: 'blue' });
        } else {
            const number = parseInt(document.getElementById('numberValue').value);
            
            if (isNaN(number) || number < 1) {
                alert("Please enter a valid number");
                return;
            }
            
            displayTokenSequential(number, { label: serviceConfig.serviceName, color: 'blue' });
        }
    }
}

/**
 * Display tokens for range-based service
 */
function displayTokensRange(start, end, labelInfo) {
    const tokenDisplay = document.getElementById('tokenDisplay');
    if (!tokenDisplay) return;
    
    tokenDisplay.innerHTML = '';
    
    // Build token data array
    const tokenData = [];
    for (let i = start; i <= end; i++) {
        const div = document.createElement('div');
        div.className = 'token';
        div.textContent = i;
        div.style.backgroundColor = labelInfo.color;
        tokenDisplay.appendChild(div);
        
        tokenData.push({
            number: i,
            color: labelInfo.color
        });
    }
    
    // Broadcast update
    if (serviceConfig.broadcastRoute) {
        broadcastUpdate({
            start: start,
            end: end,
            label: labelInfo.label,
            tokenData: tokenData
        });
    }
    
    // Update second screen if open
    updateSecondScreen(start, end, labelInfo, 'range');
}

/**
 * Display token for sequential service
 */
function displayTokenSequential(number, labelInfo) {
    const tokenDisplay = document.getElementById('tokenDisplay');
    if (!tokenDisplay) return;
    
    tokenDisplay.innerHTML = '';
    
    const div = document.createElement('div');
    div.className = 'token';
    div.textContent = number;
    div.style.backgroundColor = labelInfo.color;
    tokenDisplay.appendChild(div);
    
    const tokenData = [{
        number: number,
        color: labelInfo.color
    }];
    
    // Broadcast update
    if (serviceConfig.broadcastRoute) {
        broadcastUpdate({
            number: number,
            label: labelInfo.label,
            tokenData: tokenData
        });
    }
    
    // Update second screen if open
    updateSecondScreen(number, null, labelInfo, 'sequential');
}

/**
 * Broadcast service update to WebSocket
 */
function broadcastUpdate(data) {
    if (!serviceConfig.broadcastRoute) return;
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    fetch(serviceConfig.broadcastRoute, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            console.error('Failed to broadcast update:', data.message);
        }
    })
    .catch(error => {
        console.error('Error broadcasting update:', error);
    });
}

/**
 * Update second screen display
 */
function updateSecondScreen(startOrNumber, end, labelInfo, type) {
    if (!secondScreen || secondScreen.closed) return;
    
    try {
        const tokenDisplay = secondScreen.document.getElementById('tokenDisplay');
        if (!tokenDisplay) return;
        
        tokenDisplay.innerHTML = '';
        
        if (type === 'range') {
            for (let i = startOrNumber; i <= end; i++) {
                const div = secondScreen.document.createElement('div');
                div.className = 'token';
                div.textContent = i;
                div.style.backgroundColor = labelInfo.color;
                tokenDisplay.appendChild(div);
            }
        } else {
            const div = secondScreen.document.createElement('div');
            div.className = 'token';
            div.textContent = startOrNumber;
            div.style.backgroundColor = labelInfo.color;
            tokenDisplay.appendChild(div);
        }
    } catch (e) {
        // Cross-origin or closed window
        console.warn('Cannot update second screen:', e);
    }
}

/**
 * Handle opening second screen
 */
async function handleOpenSecondScreen() {
    if (!secondScreen || secondScreen.closed) {
        // Register screen with database and get signed URL
        let signedUrl = null;
        if (typeof screenHeartbeat !== 'undefined') {
            const screenToken = await screenHeartbeat.register('service', serviceConfig.serviceId);
            if (screenToken) {
                // Get signed URL from stored URLs (set during registration)
                signedUrl = screenHeartbeat.getSignedUrl('service', screenToken);
            }
        }
        
        // Use signed URL if available, otherwise fallback to route with token
        const url = signedUrl || (serviceConfig.secondScreenRoute + '?token=' + (screenToken || ''));
        secondScreen = window.open(url, "secondScreen", `width=${screen.availWidth},height=${screen.availHeight}`);
        
        if (secondScreen) {
            localStorage.setItem('secondScreen', secondScreen.name);
            // Wait for the window to load
            const checkLoaded = setInterval(() => {
                try {
                    if (secondScreen && !secondScreen.closed && secondScreen.document && secondScreen.document.readyState === 'complete') {
                        clearInterval(checkLoaded);
                    }
                } catch (e) {
                    clearInterval(checkLoaded);
                }
            }, 100);
            
            secondScreen.addEventListener('load', () => {
                clearInterval(checkLoaded);
            });
        } else {
            alert('Please allow pop-ups for this site to use the second screen feature.');
        }
    }
}


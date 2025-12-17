<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pair TV Screen - SmartQueue Hospital</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
        .qr-code-container {
            min-height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-900 via-blue-900 to-slate-800 flex items-center justify-center p-4">
    <div class="max-w-2xl w-full">
        <div class="bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl border border-white/20 p-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-2">
                    Pair TV Screen
                </h1>
                <p class="text-gray-600 text-lg">
                    Scan this QR code with your TV's camera or browser
                </p>
                @if($clinicName)
                    <p class="text-gray-500 text-sm mt-2">
                        Clinic: <span class="font-semibold">{{ $clinicName }}</span>
                    </p>
                @endif
            </div>

            <!-- QR Code -->
            <div class="bg-white rounded-xl p-6 mb-6 border-2 border-gray-200">
                <div id="qrcode" class="qr-code-container"></div>
            </div>

            <!-- Instructions -->
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg mb-6">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-blue-500 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="flex-1">
                        <h3 class="text-blue-900 font-semibold mb-2">How to Pair:</h3>
                        <ol class="list-decimal list-inside text-blue-800 space-y-1 text-sm">
                            <li>Open the camera app or browser on your TV</li>
                            <li>Point it at the QR code above</li>
                            <li>The screen will automatically load</li>
                            <li>This QR code expires in 15 minutes</li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- Direct URL (for manual entry) -->
            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                <p class="text-xs text-gray-600 mb-2 font-semibold">OR Enter URL Manually:</p>
                <div class="flex items-center gap-2">
                    <input 
                        type="text" 
                        id="screenUrl" 
                        value="{{ $screenUrl }}" 
                        readonly 
                        class="flex-1 px-3 py-2 text-xs bg-white border border-gray-300 rounded-md font-mono"
                    >
                    <button 
                        onclick="copyUrl()" 
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-md transition-colors"
                    >
                        Copy
                    </button>
                </div>
            </div>

            <!-- Expiry Warning -->
            <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded-r-lg">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-amber-500 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <p class="text-amber-800 text-sm">
                        <strong>Note:</strong> This pairing link expires in 15 minutes. Generate a new QR code if needed.
                    </p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-6 text-white/70 text-sm">
            SmartQueue Hospital Queue Management System
        </div>
    </div>

    <script>
        // Generate QR code
        const screenUrl = @json($screenUrl);
        const qrCodeElement = document.getElementById('qrcode');

        QRCode.toCanvas(screenUrl, {
            width: 300,
            margin: 2,
            color: {
                dark: '#000000',
                light: '#FFFFFF'
            }
        }, function (error, canvas) {
            if (error) {
                console.error('QR code generation error:', error);
                qrCodeElement.innerHTML = '<p class="text-red-600">Error generating QR code. Please use the URL below.</p>';
            } else {
                qrCodeElement.innerHTML = '';
                qrCodeElement.appendChild(canvas);
            }
        });

        // Copy URL function
        function copyUrl() {
            const urlInput = document.getElementById('screenUrl');
            urlInput.select();
            urlInput.setSelectionRange(0, 99999); // For mobile devices
            
            try {
                navigator.clipboard.writeText(urlInput.value);
                const button = event.target;
                const originalText = button.textContent;
                button.textContent = 'Copied!';
                button.classList.add('bg-green-600');
                button.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                
                setTimeout(() => {
                    button.textContent = originalText;
                    button.classList.remove('bg-green-600');
                    button.classList.add('bg-blue-600', 'hover:bg-blue-700');
                }, 2000);
            } catch (err) {
                // Fallback for older browsers
                document.execCommand('copy');
                alert('URL copied to clipboard!');
            }
        }

        // Check if URL is expired (client-side check, server-side validation is the real check)
        const urlParams = new URLSearchParams(new URL(screenUrl).search);
        const expires = urlParams.get('expires');
        if (expires) {
            const expiryTime = parseInt(expires) * 1000; // Convert to milliseconds
            const timeUntilExpiry = expiryTime - Date.now();
            
            if (timeUntilExpiry > 0) {
                setTimeout(() => {
                    document.querySelector('.bg-amber-50').innerHTML = `
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-red-500 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-red-800 text-sm font-semibold">
                                This pairing link has expired. Please generate a new QR code.
                            </p>
                        </div>
                    `;
                    document.querySelector('.bg-amber-50').classList.remove('bg-amber-50', 'border-amber-500');
                    document.querySelector('.bg-amber-50').classList.add('bg-red-50', 'border-red-500');
                }, timeUntilExpiry);
            }
        }
    </script>
</body>
</html>


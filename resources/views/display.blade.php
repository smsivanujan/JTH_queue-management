<html lang="en">

<head>
    <!-- Basic Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Teaching Hospital Jaffna</title>

    <!-- SEO Meta Tags -->
    <meta name="keywords" content="Health Information Unit, Teaching Hospital Jaffna">
    <meta name="description"
        content="Health Information Unit (HIU) at Teaching Hospital Jaffna ensures reliable IT infrastructure for efficient healthcare delivery.">
    <meta name="author" content="Health Information Unit, Teaching Hospital Jaffna">

    <!-- CSS Links -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css">

    <style>
        html,
        body {
            height: 100%;
            display: flex;
            flex-direction: column;
            background: linear-gradient(#373B44, #4286f4);
        }

        .header_section {
            position: sticky;
            top: 0;
            z-index: 1000;
            background-color: #ffffff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 15px 0;
        }

        .header-content {
            display: flex;
            justify-content: center;
            /* Horizontally center */
            align-items: center;
            /* Vertically center */
            height: 100%;
            /* Ensure the container has height to center vertically */
        }

        .logo {
            display: flex;
            justify-content: center;
            /* Center logo horizontally */
        }

        main {
            flex: 1;
        }

        .containerBody {
            max-width: 500px;
            background: white;
            padding: 30px;
            margin: 50px auto;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            text-align: center;
            border: 2px solid #007bff;
        }

        h1 {
            font-size: 26px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #333;
        }

        .queue-display {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: inset 0 0 8px rgba(0, 0, 0, 0.1);
        }

        .queue-display h2,
        .queue-display h3 {
            font-size: 24px;
            margin: 10px 0;
            color: #007bff;
            font-weight: bold;
        }


        /* Footer at the Bottom */
        .footer_section {
            margin-top: auto;
            background-color: #f8f9fa;
            padding: 30px 0;
        }

        /* Footer Content */
        .footer_logo {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .footer_text {
            font-size: 1rem;
            color: #555;
            margin-top: 10px;
        }

        /* Mobile Responsiveness */
        @media (max-width: 767px) {
            .service_box {
                height: auto;
                margin-bottom: 20px;
            }

            .service_section .row {
                flex-direction: column;
                align-items: center;
            }

            .footer_section .container {
                text-align: center;
            }
        }
    </style>
</head>

<body>
    <header class="header_section">
        <div class="container">
            <div class="header-content d-flex justify-content-center align-items-center py-3">
                <a class="logo" href="#">
                    <img src="http://10.1.1.25/coverpage/JTH.jpg" alt="Teaching Hospital Jaffna Logo"
                        class="img-fluid logo-img">
                </a>
            </div>
        </div>
    </header>
    <div class="containerBody">
        <h1>Hospital Queue Management</h1>

        <div class="queue-display">
            <h2>Current Number: {{ $queue->current_number }}</h2>
            <h3>Next Number: {{ $queue->next_number }}</h3>
        </div>
    </div>
    <footer class="footer_section">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 d-flex align-items-center">
                    <div class="me-4">
                        <img src="http://10.1.1.25/coverpage/hiu.png" alt="HIU Logo" class="img-fluid rounded-circle"
                            style="max-width: 100px; height: 100px;">
                    </div>
                    <div>
                        <h2 class="footer_logo">HIU at Teaching Hospital</h2>
                        <p>The Health Information Unit (HIU) at Teaching Hospital Jaffna ensures reliable IT infrastructure, including server and network management, X-ray systems, PC maintenance, website updates, and custom application development, to support efficient healthcare delivery.</p>
                    </div>
                </div>
                <div class="col-lg-4">
                    <h2>Contact Info</h2>
                    <p><i class="fa fa-map-marker"></i> Ground Floor, JAICA Building, Teaching Hospital, Jaffna</p>
                    <p><i class="fa fa-phone"></i> Call: 449</p>
                    <p><i class="fa fa-envelope"></i> Email: hiuunit693@gmail.com</p>
                </div>
            </div>
        </div>
    </footer>
    <script>
        let secondScreen = null;

        // Open the second screen (popup) to display the queue
        function openSecondScreen() {
            if (!secondScreen || secondScreen.closed) {
                secondScreen = window.open('', 'secondScreen', 'width=400,height=300');
            }
            updateSecondScreen();
        }

        // Update the second screen with the latest queue data
        function updateSecondScreen() {
            if (secondScreen) {
                secondScreen.document.body.innerHTML = `
                <h1>Queue Information</h1>
                <p>Current Number: {{ $queue->current_number }}</p>
                <p>Next Number: {{ $queue->next_number }}</p>
            `;
            }
        }

        // Trigger update when the session passes queue-updated flag
        @if(session('queue-updated'))
        localStorage.setItem('queue-update', 'updated');
        @endif

        // Listen for changes in localStorage and update the second screen when needed
        window.addEventListener('storage', function(e) {
            if (e.key === 'queue-update') {
                updateSecondScreen();
            }
        });

        // Make sure the second screen is opened
        openSecondScreen();
    </script>

</body>

</html>
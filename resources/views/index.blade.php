@extends('layouts.app')

@section('title', 'SmartQueue Hospital')

@push('styles')
<style>
    /* Basic Styling */
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
        align-items: center;
        height: 100%;
    }

    .logo {
        display: flex;
        justify-content: center;
    }

    main {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 20px;
    }

    .containerBodyWrapper {
        display: flex;
        justify-content: space-around;
        /* Distribute space evenly between items */
        gap: 20px;
        /* Space between containers */
        flex-wrap: wrap;
        /* Ensure the containers wrap when space is tight */
    }

    .containerBody {
        flex: 0 1 30%;
        max-width: 32%;
        /* Maximum width of 30% for each container */
        box-sizing: border-box;
        min-width: 450px;
        /* Prevent containers from getting too small */
        margin-bottom: 20px;
        /* Space below each container */
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        text-align: center;
        border: 2px solid #007bff;
    }

    /* Make the header text large and bold */
    h1 {
        font-size: 26px;
        font-weight: bold;
        margin-bottom: 20px;
        color: #333;
    }

    /* Styling the Queue Display */
    .queue-display {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        box-shadow: inset 0 0 8px rgba(0, 0, 0, 0.1);
    }

    /* Styling the buttons */
    .button-group {
        display: flex;
        justify-content: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    button {
        padding: 12px 18px;
        font-size: 16px;
        font-weight: bold;
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: transform 0.2s ease, opacity 0.3s ease;
    }

    button:hover {
        transform: translateY(-2px);
        opacity: 0.9;
    }

    /* Footer Section */
    .footer_section {
        margin-top: auto;
        background-color: #f8f9fa;
        padding: 30px 0;
    }

    .footer_logo {
        font-size: 1.5rem;
        font-weight: bold;
    }

    .footer_text {
        font-size: 1rem;
        color: #555;
        margin-top: 10px;
    }

    /* Media Queries for Responsive Design */
    @media screen and (max-width: 768px) {
        .containerBody {
            flex: 1 1 45%;
            /* On smaller screens, take 45% of the available space */
        }
    }

    @media screen and (max-width: 480px) {
        .containerBody {
            flex: 1 1 100%;
            /* On very small screens, stack containers vertically */
        }
    }

    footer {
        margin-top: auto;
        background-color: #f8f9fa;
        padding: 30px 0;
    }
</style>
@endpush

@section('content')
<h1 style="text-align: center; color: white;font-size: 48px;">{{ $clinic->name }}</h1>
<div id="main-queue-display"></div>
@php
$colors = ['white', 'green', 'red', 'yellow', 'brown'];
$labels = ['Urine Test', 'Full Blood Count', 'ESR'];
@endphp
<div class="containerBodyWrapper">
    @for ($i = 1; $i <= $queue->display; $i++)
        @php
        $subQueue = \App\Models\SubQueue::where('clinic_id', $queue->id)->where('queue_number', $i)->first();
        $bgColor = $colors[($i - 1) % count($colors)];
        @endphp

        <div class="containerBody" style="margin-bottom: 40px; background-color: {{ $bgColor }};">
            <h2>
                @if ($queue->display == 3 && $clinic->id == 2)
                {{ $labels[$i - 1] }}
                @else
                Queue #{{ $i }}
                @endif
            </h2>

            <div class="queue-display" id="queue-display-{{ $i }}">
                <p style="font-size: 24px; color: green; font-weight: bold;">Current Number</p>
                <p style="font-size: 48px; color: green; font-weight: bold;" id="current-number-{{ $i }}">{{ $subQueue->current_number ?? 1 }}</p>
                <p style="font-size: 12px; color: blue; font-weight: bold;">Next Number:</p>
                <p style="font-size: 18px; color: blue; font-weight: bold;" id="next-number-{{ $i }}">{{ $subQueue->next_number ?? 2 }}</p>
            </div>

            <!-- <div class="button-group">
                <form action="{{ route('queues.next', ['clinicId' => $queue->id, 'queueNumber' => $i]) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" style="background: #28a745;">Next</button>
                </form>
                <form action="{{ route('queues.previous', ['clinicId' => $queue->id, 'queueNumber' => $i]) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" style="background: #ffc107;">Previous</button>
                </form>
                <form action="{{ route('queues.reset', ['clinicId' => $queue->id, 'queueNumber' => $i]) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" style="background: #dc3545;">Reset</button>
                </form>
                <button onclick="openSecondScreen()" class="btn btn-success">
                    Open Second Screen
                </button>
            </div> -->
            <div class="button-group">
                <form id="next-form-{{ $i }}" action="{{ route('queues.next', ['clinicId' => $queue->id, 'queueNumber' => $i]) }}" method="POST" style="display:inline;">
                    @csrf
                </form>
                <form id="previous-form-{{ $i }}" action="{{ route('queues.previous', ['clinicId' => $queue->id, 'queueNumber' => $i]) }}" method="POST" style="display:inline;">
                    @csrf
                </form>
                <form id="reset-form-{{ $i }}" action="{{ route('queues.reset', ['clinicId' => $queue->id, 'queueNumber' => $i]) }}" method="POST" style="display:inline;">
                    @csrf
                </form>

                <button type="button" onclick="submitQueueAction('next', {{ $i }})" style="background: #28a745;">Next</button>
                <button type="button" onclick="submitQueueAction('previous', {{ $i }})" style="background: #ffc107;">Previous</button>
                <button type="button" onclick="submitQueueAction('reset', {{ $i }})" style="background: #dc3545;">Reset</button>

                <button onclick="openSecondScreen()" class="btn btn-info">Open Second Screen</button>
                <button onclick="recallSpeech()" class="btn btn-info">Recall Number</button>
            </div>
        </div>
        @endfor
</div>

<div style="display: flex; justify-content: center; margin-top: 20px;">
    <form action="{{ route('logout') }}" method="POST" id="logoutForm">
        @csrf
        <button type="submit" style="background:rgb(110, 110, 110);">EXIT</button>
    </form>
</div>

@endsection

@push('scripts')
<script>
    function submitQueueAction(action, queueId) {
        var form = document.getElementById(action + '-form-' + queueId);

        var formData = new FormData(form);

        // Perform AJAX request
        fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': formData.get('_token')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // alert('Action successful');
                    // You can update any parts of the page here without reloading.
                    // For example, update queue numbers, etc.
                    // Optionally, you can trigger other UI changes based on the response
                } else {
                    // alert('Action failed');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // alert('Something went wrong');
            });
    }
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const logoutForm = document.getElementById('logoutForm');

        logoutForm.addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent form submission

            // Get second screen reference from localStorage
            const secondScreenName = localStorage.getItem('secondScreen');
            const secondScreen = secondScreenName ? window.open('', secondScreenName) : null;

            // Close the second screen if it's open
            if (secondScreen && !secondScreen.closed) {
                secondScreen.close();
            }

            // Clear the second screen reference from localStorage
            localStorage.removeItem('secondScreen');

            // Submit the logout form
            logoutForm.submit();
        });
    });
</script>

<!-- //text speach -->
<script>
    // Variable to store the SpeechSynthesisUtterance
    let currentMsg;

    function speakNumber(number) {
        const msg = new SpeechSynthesisUtterance(`வரிசை எண் ${number} உள்ளே வரவும் `);
        msg.lang = 'ta-IN'; // Tamil language
        msg.rate = 0.9; // adjust speed if needed
        msg.pitch = 1;

        // Store the message so it can be recalled
        currentMsg = msg;

        // Function to repeat the speech 3 times
        let repeatCount = 3;

        function repeatSpeech() {
            if (repeatCount > 0) {
                window.speechSynthesis.cancel(); // stop any previous speech
                window.speechSynthesis.speak(msg);
                repeatCount--;
            }
        }

        // Call repeatSpeech three times
        msg.onend = repeatSpeech;
        repeatSpeech();
    }

    // Function to recall the speech
    function recallSpeech() {
        if (currentMsg) {
            speakNumber(currentMsg.text.match(/\d+/)[0]); // Recall the number from the previous speech
        } else {
            alert("No speech has been made yet!");
        }
    }
</script>


<!-- //second screen -->
<script>
    let secondScreen = null;
    let clinicQueueName = @json($clinic -> name);
    const clinicId = @json($clinic -> id);

    function openSecondScreen() {
        if (!secondScreen || secondScreen.closed) {
            secondScreen = window.open('', 'secondScreen', 'width=' + screen.availWidth + ',height=' + screen.availHeight);
            secondScreen.moveTo(0, 0);
            secondScreen.resizeTo(screen.availWidth, screen.availHeight);

            // Store secondScreen reference in localStorage
            localStorage.setItem('secondScreen', secondScreen.name);
        }
        updateSecondScreen();
    }

    function updateSecondScreen() {
        if (!secondScreen) return;

        fetch("{{ route('queues.fetchApi', ['clinicId' => '__CLINIC_ID__']) }}".replace('__CLINIC_ID__', clinicId))
            .then(res => res.json())
            .then(data => {
                let queueHtml = '';
                data.subQueues.forEach((subQueue, index) => {
                    const colors = ['white', 'green', 'red', 'yellow', 'brown'];
                    const bgColor = colors[index % colors.length];
                    const labels = [
                        'Urine Test / சிறுநீர் பரிசோதனை / මූත්‍ර පරීක්ෂණය',
                        'FBC / குருதி கல எண்ணிக்கை பரிசோதனை / රුධිර සෙලුල ගණන පරීක්ෂණය',
                        'ESR / செங்குருதி கல அடைவு பரிசோதனை / රතු රුධිර සෙලුල ප්‍රතිසංස්කරණ පරීක්ෂණය'
                    ];

                    const displayCount = @json($queue -> display);
                    const clinicIdCheck = @json($clinic -> id);

                    const queueLabel = (displayCount === 3 && clinicIdCheck === 2) ?
                        labels[index] || `Queue/வரிசை/පෝළිම` :
                        `Queue/வரிசை/පෝළිම`;

                    queueHtml += `
                        <div class="containerBody" style="margin-bottom: 40px; background-color: ${bgColor};">
                            <h4>${queueLabel}</h4>
                            <div class="queue-display">
                                <p style="font-size: 24px; color: green; font-weight: bold;">Current Number / தற்போதைய எண் / වත්மන් අංකය</p>
                                <p style="font-size: 48px; color: green; font-weight: bold;">${subQueue.current_number}</p>
                                <p style="font-size: 12px; color: blue; font-weight: bold;">Next Number / அடுத்த எண் / මීළඟ අංකය:</p>
                                <p style="font-size: 18px; color: blue; font-weight: bold;">${subQueue.next_number}</p>
                            </div>
                        </div>
                    `;
                });


                // Then render full second screen
                secondScreen.document.open();
                secondScreen.document.write(`
                    <html>
                    <head>
                        <title>Teaching Hospital Jaffna</title>
                        <meta charset="utf-8">
                        <meta http-equiv="X-UA-Compatible" content="IE=edge">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
                        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css">
                        <style>
                            /* Basic Styling */
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
                                align-items: center;
                                height: 100%;
                            }

                            .logo {
                                display: flex;
                                justify-content: center;
                            }

                            main {
                                flex: 1;
                                display: flex;
                                flex-direction: column;
                                align-items: center;
                                padding: 20px;
                            }

                            .containerBodyWrapper {
                                display: flex;
                                justify-content: space-around;
                                /* Distribute space evenly between items */
                                gap: 20px;
                                /* Space between containers */
                                flex-wrap: wrap;
                                /* Ensure the containers wrap when space is tight */
                            }

                            .containerBody {
                                flex: 1 1 30%;
                                /* Allow the container to take 30% of available space */
                                max-width: 30%;
                                /* Maximum width of 30% for each container */
                                box-sizing: border-box;
                                min-width: 250px;
                                /* Prevent containers from getting too small */
                                margin-bottom: 20px;
                                /* Space below each container */
                                background: white;
                                padding: 30px;
                                border-radius: 12px;
                                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
                                text-align: center;
                                border: 2px solid #007bff;
                            }

                            /* Make the header text large and bold */
                            h1 {
                                font-size: 26px;
                                font-weight: bold;
                                margin-bottom: 20px;
                                color: #333;
                            }

                            /* Styling the Queue Display */
                            .queue-display {
                                background: #f8f9fa;
                                padding: 20px;
                                border-radius: 10px;
                                margin-bottom: 20px;
                                box-shadow: inset 0 0 8px rgba(0, 0, 0, 0.1);
                            }

                            /* Styling the buttons */
                            .button-group {
                                display: flex;
                                justify-content: center;
                                gap: 10px;
                                flex-wrap: wrap;
                            }

                            button {
                                padding: 12px 18px;
                                font-size: 16px;
                                font-weight: bold;
                                color: white;
                                border: none;
                                border-radius: 8px;
                                cursor: pointer;
                                transition: transform 0.2s ease, opacity 0.3s ease;
                            }

                            button:hover {
                                transform: translateY(-2px);
                                opacity: 0.9;
                            }

                            /* Footer Section */
                            .footer_section {
                                margin-top: auto;
                                background-color: #f8f9fa;
                                padding: 30px 0;
                            }

                            .footer_logo {
                                font-size: 1.5rem;
                                font-weight: bold;
                            }

                            .footer_text {
                                font-size: 1rem;
                                color: #555;
                                margin-top: 10px;
                            }

                            /* Media Queries for Responsive Design */
                            @media screen and (max-width: 768px) {
                                .containerBody {
                                    flex: 1 1 45%;
                                    /* On smaller screens, take 45% of the available space */
                                }
                            }

                            @media screen and (max-width: 480px) {
                                .containerBody {
                                    flex: 1 1 100%;
                                    /* On very small screens, stack containers vertically */
                                }
                            }

                            /* Optional: To make the footer stay at the bottom */
                            html,
                            body {
                                display: flex;
                                flex-direction: column;
                                height: 100%;
                            }

                            footer {
                                margin-top: auto;
                                background-color: #f8f9fa;
                                padding: 30px 0;
                            }
                        </style>
                    </head>
                    <body>
                        <header style="text-align: center; background: white; padding: 20px;">
                            <img src="http://10.1.1.25/coverpage/JTH.jpg" style="max-height: 100px;" />
                        </header>
                        <h1 style="text-align: center; color: white;font-size: 48px;">${clinicQueueName}</h1>
                        <div class="containerBodyWrapper">             
                            ${queueHtml}
                        </div>
                        <footer class="footer_section" style="background: white; padding: 40px;">
                            <div class="container">
                                <div class="row">
                                    <div class="col-lg-8 d-flex align-items-center">
                                        <div class="me-4">
                                            <img src="http://10.1.1.25/coverpage/hiu.png" alt="HIU Logo" class="img-fluid rounded-circle"
                                                style="max-width: 100px; height: 100px;">
                                        </div>
                                        <div>
                                            <h2 class="footer_logo">Health Information Unit (HIU)</h2>
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
                    </body>
                    </html>
            `);
                secondScreen.document.close();
            });
    }

    let currentQueueNumbers = {};

    function fetchQueueLive() {
        fetch("{{ url('/api/queue/' . $queue->id) }}")
            .then(res => res.json())
            .then(data => {
                // Loop through each sub-queue and update its current number
                for (let i = 1; i <= data.subQueues.length; i++) {
                    const subQueue = data.subQueues[i - 1]; // Sub-queue data for this index

                    // Only update if the current number has changed
                    if (subQueue.current_number !== currentQueueNumbers[i]) {
                        speakNumber(subQueue.current_number);
                    }

                    // Store the current number for comparison in the next iteration
                    currentQueueNumbers[i] = subQueue.current_number;

                    // Update the HTML for each sub-queue dynamically
                    const currentNumberElement = document.getElementById(`current-number-${i}`);
                    const nextNumberElement = document.getElementById(`next-number-${i}`);

                    if (currentNumberElement) {
                        currentNumberElement.innerHTML = subQueue.current_number;
                    }

                    if (nextNumberElement) {
                        nextNumberElement.innerHTML = subQueue.next_number;
                    }
                }

                updateSecondScreen();
            })
            .catch(error => console.error("Error fetching queue data:", error));
    }

    // Run every 3 seconds
    setInterval(fetchQueueLive, 3000);
    fetchQueueLive();
    // openSecondScreen();
</script>
@endpush
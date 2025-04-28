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

    .token-display {
        margin-top: 1px;
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 5px;
    }

    .token {
        background: white;
        color: black;
        font-size: 12px;
        padding: 15px 15px;
        border-radius: 10px;
        border: 2px solid #007bff;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        min-width: 10px;
        /* optional: fix token width */
        text-align: center;
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
<h1 style="text-align: center; color: white; font-size: 48px;">OPD LAB</h1>

<div class="containerBody" style="margin-bottom: 40px; background-color:gray;">
    <h2>OPD LAB QUEUE</h2>

    <div class="queue-display" id="queue-display">
        <input type="number" id="text1" style="font-size: 24px; color: green; font-weight: bold;" placeholder="Start Value" />
        <input type="number" id="text2" style="font-size: 24px; color: green; font-weight: bold;" placeholder="End Value" />
    </div>

    <div class="button-group">
        <button style="background:rgb(230, 235, 231);" class="test-btn urine-btn">URINE TEST</button>
        <button style="background:rgb(63, 158, 85);" class="test-btn fbc-btn">FBC</button>
        <button style="background:rgb(243, 17, 17);" class="test-btn esr-btn">ESR</button>

        <button onclick="openSecondScreen()" class="btn btn-info">Open Second Screen</button>
    </div>
</div>

<div class="containerBodyWrapper">
    <div class="containerBody">
        <div id="tokenDisplay" class="token-display"></div>
        <div id="testLabel" class="test-label"></div>
    </div>
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
    let secondScreen = null;

    document.addEventListener('DOMContentLoaded', function() {
        const logoutForm = document.getElementById('logoutForm');
        logoutForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const secondScreenName = localStorage.getItem('secondScreen');
            const secondWindow = secondScreenName ? window.open('', secondScreenName) : null;
            if (secondWindow && !secondWindow.closed) {
                secondWindow.close();
            }
            localStorage.removeItem('secondScreen');
            logoutForm.submit();
        });
    });

    function speakNumber(startNumber, endNumber, testLabel) {
        const msg = new SpeechSynthesisUtterance(`வரிசை எண் ${startNumber} லிருந்து ${endNumber} வரை வரவும். பரிசோதனை: ${testLabel}`);
        msg.lang = 'ta-IN';
        msg.rate = 0.9;
        msg.pitch = 1;
        let repeatCount = 3;

        msg.onend = function() {
            if (--repeatCount > 0) {
                window.speechSynthesis.speak(msg);
            }
        };
        window.speechSynthesis.cancel();
        window.speechSynthesis.speak(msg);
    }

    function displayTokensWithColor(testLabel, color) {
        const startValue = parseInt(document.getElementById('text1').value);
        const endValue = parseInt(document.getElementById('text2').value);

        if (isNaN(startValue) || isNaN(endValue)) {
            alert("Please enter valid numbers.");
            return;
        }
        if (startValue > endValue) {
            alert("Start value must be less than end value.");
            return;
        }

        document.getElementById('testLabel').textContent = testLabel;
        const tokenDisplay = document.getElementById('tokenDisplay');
        tokenDisplay.innerHTML = '';

        for (let i = startValue; i <= endValue; i++) {
            const token = document.createElement('div');
            token.classList.add('token');
            token.textContent = `${i}`;
            token.style.backgroundColor = color;
            tokenDisplay.appendChild(token);
        }

        if (secondScreen && !secondScreen.closed) {
            secondScreen.document.getElementById('secondTestLabel').textContent = testLabel;
            const secondTokenDisplay = secondScreen.document.getElementById('secondTokenDisplay');
            secondTokenDisplay.innerHTML = '';
            for (let i = startValue; i <= endValue; i++) {
                const token = secondScreen.document.createElement('div');
                token.classList.add('token');
                token.textContent = `${i}`;
                token.style.backgroundColor = color;
                secondTokenDisplay.appendChild(token);
            }
        }

        speakNumber(startValue, endValue, testLabel);
    }

    document.querySelector('.urine-btn').onclick = function() {
        displayTokensWithColor('Urine Test', 'white');
    };
    document.querySelector('.fbc-btn').onclick = function() {
        displayTokensWithColor('Full Blood Count', 'green');
    };
    document.querySelector('.esr-btn').onclick = function() {
        displayTokensWithColor('ESR', 'red');
    };

    function openSecondScreen() {
        if (!secondScreen || secondScreen.closed) {
            secondScreen = window.open('', 'secondScreen', 'width=' + screen.availWidth + ',height=' + screen.availHeight);
            secondScreen.moveTo(0, 0);
            secondScreen.resizeTo(screen.availWidth, screen.availHeight);
            localStorage.setItem('secondScreen', secondScreen.name);

            secondScreen.document.open();
            secondScreen.document.write(`
                <html>
                    <head>
                        <title>Teaching Hospital Jaffna</title>
                        <meta charset="utf-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                                justify-content: center; /* Center the items horizontally */
                                gap: 20px; /* Space between containers */
                                flex-wrap: wrap; /* Ensure the containers wrap when space is tight */
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

                            .token-display {
                                margin-top: 20px;
                                display: flex;
                                flex-wrap: wrap;
                                justify-content: center; /* center the tokens */
                                gap: 15px; /* space between tokens */
                            }
                            
                            .token {
                                background: white;
                                color: black;
                                font-size: 24px;
                                padding: 15px 30px;
                                border-radius: 10px;
                                border: 2px solid #007bff;
                                box-shadow: 0 4px 8px rgba(0,0,0,0.2);
                                min-width: 120px; /* optional: fix token width */
                                text-align: center;
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
                    </head>
                    <body>
                        <header style="text-align: center; background: white; padding: 20px;">
                            <img src="http://10.1.1.25/coverpage/JTH.jpg" style="max-height: 100px;" />
                        </header>
                        <h1 style="text-align: center; color: white;font-size: 48px;">OPD LAB</h1>
                        <h2 style="text-align: center; color: white;font-size: 48px;" id="secondTestLabel" >df</h2>
                        <div class="containerBodyWrapper">             
                            
                            <div id="secondTokenDisplay" class="containerBody"></div>
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
        }
    }
</script>
@endpush
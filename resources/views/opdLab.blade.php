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

<div class="containerBody" style="margin-bottom: 40px;">
    <div style="display: flex; flex-direction: row; justify-content: center; align-items: flex-start; gap: 20px; margin: 20px 0; flex-wrap: nowrap;">
        <div style="flex: 0 0 auto; display: flex; flex-direction: column;">
            <label for="text1" style="font-size: 18px; font-weight: 600; margin-bottom: 5px; color: #333;">Start Value</label>
            <input
                type="number"
                id="text1"
                placeholder="Start Value"
                min="0"
                style="font-size: 24px; font-weight: bold; color: green; padding: 10px 15px; border: 2px solid #ccc; border-radius: 8px; width: 220px; box-shadow: 0 2px 6px rgba(0,0,0,0.1);" />
        </div>
        <div style="flex: 0 0 auto; display: flex; flex-direction: column;">
            <label for="text2" style="font-size: 18px; font-weight: 600; margin-bottom: 5px; color: #333;">End Value</label>
            <input
                type="number"
                id="text2"
                placeholder="End Value"
                min="0"
                style="font-size: 24px; font-weight: bold; color: green; padding: 10px 15px; border: 2px solid #ccc; border-radius: 8px; width: 220px; box-shadow: 0 2px 6px rgba(0,0,0,0.1);" />
        </div>
    </div>

    <div class="button-group" style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
        <select id="testSelect" style="width: 200px; margin: 10px 0; padding: 10px; font-weight: bold; height: 45px;">
            <option value="">-- Select Test --</option>
            <option value="Urine Test" style="width: 200px; margin: 10px 0; padding: 10px; font-weight: bold; height: 45px;" data-color="white">Urine Test</option>
            <option value="ESR" style="width: 200px; margin: 10px 0; padding: 10px; font-weight: bold; height: 45px;" data-color="red">ESR</option>
            <option value="Full Blood Count" style="width: 200px; margin: 10px 0; padding: 10px; font-weight: bold; height: 45px;" data-color="green">FBC</option>
        </select>


        <button onclick="handleTestChange()" style="background:rgb(243, 17, 17); height: 45px; padding: 0 15px; font-weight: bold; color: white; border: none; border-radius: 6px; cursor: pointer;">
            CALL
        </button>

        <button onclick="openSecondScreen()" class="btn btn-info" style="height: 45px; padding: 0 15px; font-weight: bold;">
            Open Second Screen
        </button>
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
    function handleTestChange() {
        const select = document.getElementById("testSelect");
        const testLabel = select.value;

        if (!testLabel) {
            alert("Please select a test");
            return;
        }

        const selectedOption = select.options[select.selectedIndex];
        const color = selectedOption.getAttribute("data-color");

        displayTokensWithColor(testLabel, color);
    }
</script>

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

    function speakNumber(startNumber, endNumber, testLabel, labelcolor) {
        // const msg = new SpeechSynthesisUtterance(`${labelcolor} நிற வரிசை எண்கள் ${startNumber} லிருந்து ${endNumber} வரை ${testLabel} முடிவுகளை பெற்றுக்கொள்ள வரவும்.`); //பரிசோதனை: ${testLabel}
        let msg;

        if (startNumber === endNumber) {
            msg = new SpeechSynthesisUtterance(`${labelcolor} நிற வரிசை எண் ${startNumber} ஆனது  ${testLabel} முடிவுகளை பெற்றுக்கொள்ள வரவும்.`);
        } else {
            msg = new SpeechSynthesisUtterance(`${labelcolor} நிற வரிசை எண்கள் ${startNumber} லிருந்து ${endNumber} வரை ${testLabel} முடிவுகளை பெற்றுக்கொள்ள வரவும்.`);
        }

        window.speechSynthesis.speak(msg);

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

        // Setting the test label with all three languages
        const testLabelContent = {
            'Urine Test': {
                en: 'Urine Test',
                ta: 'சிறுநீர் பரிசோதனை',
                si: 'මූත්‍ර පරීක්ෂණය',
                encolor: 'white',
                tacolor: 'வெள்ளை'
            },
            'Full Blood Count': {
                en: 'FBC',
                ta: 'குருதி கல எண்ணிக்கை பரிசோதனை',
                si: 'රුධිර සෙලුල ගණන පරීක්ෂණය',
                encolor: 'green',
                tacolor: 'பச்சை'
            },
            'ESR': {
                en: 'ESR',
                ta: 'செங்குருதி கல அடைவு பரிசோதனை',
                si: 'රතු රුධිර සෙලුල ප්‍රතිසංස්කරණ පරීක්ෂණය',
                encolor: 'red',
                tacolor: 'சிவப்பு'
            }
        };

        // Fetching the translated test name based on the test label
        const label = testLabelContent[testLabel];
        const labelText = `${label.en} / ${label.ta} / ${label.si}`;
        const CalllabelText = label.ta;
        const labelcolor = label.tacolor;

        if (label) {
            document.getElementById('testLabel').textContent = labelText;
        } else {
            document.getElementById('testLabel').textContent = testLabel; // Default to just testLabel if not found
        }

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
            // secondScreen.document.getElementById('secondTestLabel').textContent = labelText;
            const testLabel = secondScreen.document.getElementById('secondTestLabel');
            if (testLabel) {
                testLabel.textContent = labelText;
                testLabel.style.color = color;
            }
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

        speakNumber(startValue, endValue, CalllabelText, labelcolor);

        document.getElementById("text1").value = "";
        document.getElementById("text2").value = "";
        document.getElementById("testSelect").value = "";
    }

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
                        <!-- Basic Meta Tags -->
                        <meta charset="utf-8">
                        <meta http-equiv="X-UA-Compatible" content="IE=edge">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <title>@yield('title', 'Teaching Hospital Jaffna')</title>

                        <!-- SEO Meta Tags -->
                        <meta name="keywords" content="Health Information Unit, Teaching Hospital Jaffna">
                        <meta name="description"
                            content="Health Information Unit (HIU) at Teaching Hospital Jaffna ensures reliable IT infrastructure for efficient healthcare delivery.">
                        <meta name="author" content="Health Information Unit, Teaching Hospital Jaffna">

                        <!-- CSS Links -->
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
                                padding: 15px 15px;
                                border-radius: 10px;
                                border: 2px solid #007bff;
                                box-shadow: 0 4px 8px rgba(0,0,0,0.2);
                                min-width: 10px; /* optional: fix token width */
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
                        <h2 style="text-align: center; color: white;font-size: 32px;" id="secondTestLabel" class="test-label">WAIT</h2>
                        <div class="containerBodyWrapper">             
                            <div id="secondTokenDisplay" class="containerBody token-display"></div>
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
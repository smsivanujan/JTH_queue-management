<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Queue Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            text-align: center;
            margin-top: 50px;
        }
        .queue-display {
            font-size: 30px;
            margin: 20px;
        }
        .button-group {
            margin-top: 30px;
        }
        button {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            margin: 5px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Hospital Queue Management</h1>
    
    <div class="queue-display">
        <h2>Current Number: {{ $queue->current_number }}</h2>
        <h3>Next Number: {{ $queue->next_number }}</h3>
    </div>

    <div class="button-group">
        <form action="{{ url('/next') }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit">Next</button>
        </form>
        <form action="{{ url('/previous') }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit">Previous</button>
        </form>
        <form action="{{ url('/reset') }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit">Reset</button>
        </form>
    </div>
</div>

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

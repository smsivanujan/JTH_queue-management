@extends('layouts.app')

@section('title', 'SmartQueue Hospital')

@push('styles')
<style>
    html, body {
        height: 100%;
        display: flex;
        flex-direction: column;
        background: linear-gradient(135deg, #373B44, #4286f4);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    main {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 20px;
    }

    h1 {
        font-size: 3rem;
        font-weight: bold;
        color: #fff;
        text-align: center;
        margin-bottom: 30px;
        text-shadow: 2px 2px 5px rgba(0,0,0,0.3);
    }

    .containerBodyWrapper {
        display: flex;
        justify-content: center;
        gap: 20px;
        flex-wrap: wrap;
        width: 100%;
    }

    .containerBody {
        flex: 1 1 30%;
        min-width: 300px;
        max-width: 350px;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        text-align: center;
        padding: 25px 15px;
        border: 2px solid #007bff;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .containerBody:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.3);
    }

    .queue-display {
        background: #f0f4f8;
        padding: 20px;
        border-radius: 12px;
        margin: 15px 0;
        box-shadow: inset 0 0 10px rgba(0,0,0,0.1);
    }

    .queue-display p {
        margin: 5px 0;
    }

    .queue-display p.current-number {
        font-size: 3rem;
        color: #28a745;
        font-weight: bold;
    }

    .queue-display p.next-number {
        font-size: 1.5rem;
        color: #007bff;
        font-weight: bold;
    }

    .button-group {
        display: flex;
        justify-content: center;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 10px;
    }

    .button-group button {
        padding: 10px 20px;
        font-size: 14px;
        font-weight: bold;
        color: #fff;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        transition: 0.3s ease;
    }

    .btn-next { background: #28a745; }
    .btn-prev { background: #ffc107; color: #000; }
    .btn-reset { background: #dc3545; }
    .btn-secondary { background: #17a2b8; }

    .button-group button:hover {
        transform: translateY(-3px);
        opacity: 0.9;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .containerBody {
            flex: 1 1 45%;
        }
    }
    @media (max-width: 480px) {
        .containerBody {
            flex: 1 1 100%;
        }
    }

</style>
@endpush

@section('content')
<h1>{{ $clinic->name }}</h1>

<div class="containerBodyWrapper">
    @for ($i = 1; $i <= $queue->display; $i++)
        @php
            $subQueue = \App\Models\SubQueue::where('clinic_id', $queue->id)
                          ->where('queue_number', $i)->first();
            $bgColors = ['#ffffff', '#e6f7ff', '#fff0f6', '#fffbe6', '#f9f0ff'];
            $bgColor = $bgColors[($i-1) % count($bgColors)];
            $labels = ['Urine Test', 'Full Blood Count', 'ESR'];
        @endphp

        <div class="containerBody" style="background-color: {{ $bgColor }};">
            <h2>
                @if ($queue->display == 3 && $clinic->id == 2)
                    {{ $labels[$i-1] }}
                @else
                    Queue #{{ $i }}
                @endif
            </h2>

            <div class="queue-display">
                <p class="current-label">Current Number</p>
                <p class="current-number" id="current-number-{{ $i }}">{{ $subQueue->current_number ?? 1 }}</p>
                <p class="next-label">Next Number</p>
                <p class="next-number" id="next-number-{{ $i }}">{{ $subQueue->next_number ?? 2 }}</p>
            </div>

            <div class="button-group">
                <form id="next-form-{{ $i }}" action="{{ route('queues.next', ['clinicId'=>$queue->id,'queueNumber'=>$i]) }}" method="POST">@csrf</form>
                <form id="previous-form-{{ $i }}" action="{{ route('queues.previous', ['clinicId'=>$queue->id,'queueNumber'=>$i]) }}" method="POST">@csrf</form>
                <form id="reset-form-{{ $i }}" action="{{ route('queues.reset', ['clinicId'=>$queue->id,'queueNumber'=>$i]) }}" method="POST">@csrf</form>

                <button type="button" class="btn-next" onclick="submitQueueAction('next', {{ $i }})">Next</button>
                <button type="button" class="btn-prev" onclick="submitQueueAction('previous', {{ $i }})">Previous</button>
                <button type="button" class="btn-reset" onclick="submitQueueAction('reset', {{ $i }})">Reset</button>
                <button type="button" class="btn-secondary" onclick="openSecondScreen()">Second Screen</button>
                <button type="button" class="btn-secondary" onclick="recallSpeech()">Recall</button>
            </div>
        </div>
    @endfor
</div>

<div style="display: flex; justify-content: center; margin-top: 30px;">
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="btn-reset" style="background:#6e6e6e;">EXIT</button>
    </form>
</div>
@endsection

@push('scripts')
<script>
    // Queue button actions
    function submitQueueAction(action, queueId){
        let form = document.getElementById(`${action}-form-${queueId}`);
        let formData = new FormData(form);

        fetch(form.action, {method:'POST', body:formData, headers:{'X-CSRF-TOKEN':formData.get('_token')}})
            .then(res=>res.json())
            .then(data=>{ /* update UI if needed */ })
            .catch(err=>console.error(err));
    }

    // Text-to-speech
    let currentMsg;
    function speakNumber(number){
        const msg = new SpeechSynthesisUtterance(`வரிசை எண் ${number} உள்ளே வரவும்`);
        msg.lang = 'ta-IN';
        msg.rate = 0.9;
        currentMsg = msg;

        let repeat=3;
        function repeatSpeech(){
            if(repeat>0){ window.speechSynthesis.cancel(); window.speechSynthesis.speak(msg); repeat--; }
        }
        msg.onend = repeatSpeech;
        repeatSpeech();
    }

    function recallSpeech(){
        if(currentMsg){ speakNumber(currentMsg.text.match(/\d+/)[0]); }
        else alert("No speech has been made yet!");
    }

    // Second screen
    let secondScreen = null;
    let clinicQueueName = @json($clinic->name);
    const clinicId = @json($clinic->id);

    function openSecondScreen(){
        if(!secondScreen || secondScreen.closed){
            secondScreen = window.open('', 'secondScreen', `width=${screen.availWidth},height=${screen.availHeight}`);
            secondScreen.moveTo(0,0); secondScreen.resizeTo(screen.availWidth, screen.availHeight);
            localStorage.setItem('secondScreen', secondScreen.name);
        }
        updateSecondScreen();
    }

    function updateSecondScreen(){
        if(!secondScreen) return;
        fetch(`{{ route('queues.fetchApi',['clinicId'=>'__CLINIC_ID__']) }}`.replace('__CLINIC_ID__',clinicId))
        .then(res=>res.json())
        .then(data=>{
            let queueHtml='';
            const colors=['#ffffff','#e6f7ff','#fff0f6','#fffbe6','#f9f0ff'];
            const labels=['Urine Test','Full Blood Count','ESR'];
            data.subQueues.forEach((sq,index)=>{
                const bgColor = colors[index%colors.length];
                const label = (@json($queue->display)===3 && @json($clinic->id)===2) ? (labels[index]||`Queue`) : `Queue`;
                queueHtml+=`
                    <div class="containerBody" style="background-color:${bgColor};">
                        <h4>${label}</h4>
                        <div class="queue-display">
                            <p class="current-label">Current Number</p>
                            <p class="current-number">${sq.current_number}</p>
                            <p class="next-label">Next Number</p>
                            <p class="next-number">${sq.next_number}</p>
                        </div>
                    </div>`;
            });

            secondScreen.document.open();
            secondScreen.document.write(`
                <html><head>
                <title>${clinicQueueName}</title>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css">
                <style>
                    html,body{height:100%;display:flex;flex-direction:column;font-family:Segoe UI;}
                    .containerBodyWrapper{display:flex;flex-wrap:wrap;justify-content:center;gap:20px;}
                    .containerBody{flex:1 1 30%;min-width:250px;max-width:350px;background:#fff;padding:25px 15px;border-radius:16px;text-align:center;border:2px solid #007bff;margin-bottom:20px;}
                    .queue-display{background:#f0f4f8;padding:20px;border-radius:12px;box-shadow:inset 0 0 10px rgba(0,0,0,0.1);}
                    h4{margin-bottom:10px;}
                </style>
                </head>
                <body>
                    <h1 style="text-align:center;color:#333;">${clinicQueueName}</h1>
                    <div class="containerBodyWrapper">${queueHtml}</div>
                </body>
                </html>
            `);
            secondScreen.document.close();
        });
    }

    // Live queue update
    let currentQueueNumbers = {};
    function fetchQueueLive(){
        fetch("{{ url('/api/queue/'.$queue->id) }}")
        .then(res=>res.json())
        .then(data=>{
            data.subQueues.forEach((sq,index)=>{
                const i=index+1;
                if(sq.current_number!==currentQueueNumbers[i]) speakNumber(sq.current_number);
                currentQueueNumbers[i]=sq.current_number;

                const curEl=document.getElementById(`current-number-${i}`);
                const nextEl=document.getElementById(`next-number-${i}`);
                if(curEl) curEl.innerHTML=sq.current_number;
                if(nextEl) nextEl.innerHTML=sq.next_number;
            });
            updateSecondScreen();
        }).catch(err=>console.error(err));
    }
    setInterval(fetchQueueLive,3000);
    fetchQueueLive();
</script>
@endpush

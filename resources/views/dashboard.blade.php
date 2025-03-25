@extends('layouts.app')

@section('title', 'SmartQueue Hospital')
@push('styles')
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
        align-items: center;
        height: 100%;

    }

    .logo {
        display: flex;
        justify-content: center;
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
        color: green;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: transform 0.2s ease, opacity 0.3s ease;
    }

    button:hover {
        transform: translateY(-2px);
        opacity: 0.9;
    }

    .clinic-image {
        height: 5em;
        width: auto;
        max-width: 100%;
        display: block;
        margin: 0 auto;
        object-fit: contain;
    }

    .modal {
        display: none;
        position: fixed;
        z-index: 2000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.4);
    }

    .modal-content {
        background-color: #fff;
        margin: 10% auto;
        padding: 30px;
        border: 1px solid #888;
        border-radius: 12px;
        width: 90%;
        max-width: 400px;
        text-align: center;
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }


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

    @media (max-width: 767px) {
        .footer_section .container {
            text-align: center;
        }
    }
</style>
@endpush
@section('content')

<div class="containerBody">
    <h1>SmartQueue Hospital</h1>
    <div class="button-group">
        @foreach ($clinics as $clinic)
        <form action="{{ route('password.check') }}" method="GET" class="clinic-form">
            <input type="hidden" name="clinic_id" value="{{ $clinic->id }}">
            <button type="submit">
                <img src="{{ asset('public/images/clinics/' . $clinic->id . '.ico') }}" alt="{{ $clinic->name }}" class="clinic-image mb-3">
                <span class="text-lg font-semibold text-blue-700">{{ $clinic->name }}</span>
            </button>
        </form>
        @endforeach
    </div>
</div>

<!-- Modal -->
<div id="passwordModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeModalBtn">&times;</span>
        <h2>Enter Password</h2>

        @if ($errors->any())
        <p style="color: red;">{{ $errors->first() }}</p>
        @endif

        <form action="{{ route('password.verify') }}" method="POST">
            @csrf
            <input type="hidden" name="clinic_id" id="modalClinicId">
            <input type="password" name="password" placeholder="Enter password" required>
            <button type="submit">Submit</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const forms = document.querySelectorAll('.clinic-form');
        const modal = document.getElementById('passwordModal');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const clinicIdInput = document.getElementById('modalClinicId'); // updated to match the input ID

        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const clinicId = this.querySelector('input[name="clinic_id"]').value;
                clinicIdInput.value = clinicId;
                modal.style.display = 'block';
            });
        });

        closeModalBtn.addEventListener('click', function() {
            modal.style.display = 'none';
        });

        window.addEventListener('click', function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        });
    });
</script>
@endpush
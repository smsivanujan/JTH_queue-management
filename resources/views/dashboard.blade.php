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

    .header_section {
        position: sticky;
        top: 0;
        z-index: 1000;
        background-color: #fff;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        padding: 15px 0;
    }

    main {
        flex: 1;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 30px 15px;
    }

    .containerBody {
        max-width: 600px;
        width: 100%;
        background: #ffffff;
        padding: 40px;
        border-radius: 16px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        text-align: center;
        border: 1px solid #007bff;
        animation: fadeIn 0.8s ease-in-out;
    }

    h1 {
        font-size: 28px;
        font-weight: bold;
        margin-bottom: 30px;
        color: #333;
    }

    .button-group {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 20px;
    }

    button {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 18px 24px;
        font-size: 16px;
        font-weight: 600;
        color: #fff;
        background: linear-gradient(135deg, #007bff, #00c6ff);
        border: none;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
        min-width: 140px;
    }

    button:hover {
        transform: translateY(-3px) scale(1.05);
        box-shadow: 0 8px 20px rgba(0,123,255,0.4);
    }

    .clinic-image {
        height: 60px;
        width: 60px;
        object-fit: contain;
        margin-bottom: 8px;
    }

    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 2000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.5);
        animation: fadeIn 0.5s ease;
    }

    .modal-content {
        background-color: #fff;
        margin: 10% auto;
        padding: 30px;
        border-radius: 16px;
        max-width: 400px;
        text-align: center;
        box-shadow: 0 8px 25px rgba(0,0,0,0.3);
    }

    .modal-content input[type="password"] {
        width: 80%;
        padding: 10px;
        margin: 15px 0;
        border-radius: 8px;
        border: 1px solid #ccc;
        font-size: 16px;
    }

    .modal-content button {
        width: 50%;
        background: #007bff;
        color: #fff;
        font-weight: bold;
        border-radius: 8px;
        padding: 10px;
        border: none;
        transition: 0.3s;
    }

    .modal-content button:hover {
        background: #0056b3;
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 767px) {
        .button-group {
            gap: 15px;
        }
        .clinic-image {
            height: 50px;
            width: 50px;
        }
    }
</style>
@endpush

@section('content')
<div class="containerBody">
    <h1>SmartQueue Hospital</h1>
    <div class="button-group">
        <!-- OPD LAB Button -->
        <button type="button" onclick="openPasswordModal()">
            <img src="{{ asset('public/images/clinics/2.ico') }}" alt="opd lab" class="clinic-image">
            OPD LAB
        </button>

        @foreach ($clinics as $clinic)
        <form action="{{ route('password.check') }}" method="GET" class="clinic-form">
            <input type="hidden" name="clinic_id" value="{{ $clinic->id }}">
            <button type="submit">
                <img src="{{ asset('public/images/clinics/' . $clinic->id . '.ico') }}" alt="{{ $clinic->name }}" class="clinic-image">
                {{ $clinic->name }}
            </button>
        </form>
        @endforeach
    </div>
</div>

<!-- Password Modal -->
<div id="passwordModal2" class="modal">
    <div class="modal-content">
        <span class="close" id="closeModalBtn2">&times;</span>
        <h2>Enter Password</h2>
        <input type="password" id="passwordInput" placeholder="Enter password">
        <button onclick="verifyPassword2()">Submit</button>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openPasswordModal() {
        document.getElementById('passwordModal2').style.display = 'block';
    }

    document.getElementById('closeModalBtn2').onclick = function() {
        document.getElementById('passwordModal2').style.display = 'none';
    }

    function verifyPassword2() {
        const password = document.getElementById('passwordInput').value;
        if(password === "1234") {
            window.location.href = "{{ route('opdLab') }}";
        } else {
            alert("Wrong password!");
        }
    }

    // Clinic password modal
    document.addEventListener('DOMContentLoaded', function() {
        const forms = document.querySelectorAll('.clinic-form');
        const modal = document.getElementById('passwordModal');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const clinicIdInput = document.getElementById('modalClinicId');

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
            if (event.target == modal) modal.style.display = 'none';
        });
    });
</script>
@endpush

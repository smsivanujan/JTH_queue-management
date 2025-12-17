@extends('layouts.app')

@section('title', 'SmartQueue Hospital')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/opdLab.css') }}">
@endpush

@section('content')
<h1 class="page-title">OPD LAB</h1>

<div class="containerBody">
    <div class="input-group">
        <div class="input-item">
            <label for="text1">Start Value</label>
            <input type="number" id="text1" placeholder="Start Value" min="0"/>
        </div>
        <div class="input-item">
            <label for="text2">End Value</label>
            <input type="number" id="text2" placeholder="End Value" min="0"/>
        </div>
    </div>

    <div class="button-group">
        <select id="testSelect">
            <option value="">-- Select Test --</option>
            <option value="Urine Test" data-color="white">Urine Test</option>
            <option value="ESR" data-color="red">ESR</option>
            <option value="Full Blood Count" data-color="green">FBC</option>
        </select>

        <button id="callBtn" class="btn-call">CALL</button>
        <button id="openSecondScreen" class="btn-secondScreen">Open Second Screen</button>
    </div>
</div>

<div class="containerBodyWrapper">
    <div class="containerBody">
        <div id="tokenDisplay" class="token-display"></div>
        <div id="testLabel" class="test-label">WAIT</div>
    </div>
</div>

<div class="exit-btn">
    <form action="{{ route('logout') }}" method="POST" id="logoutForm">
        @csrf
        <button type="submit">EXIT</button>
    </form>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/opdLab.js') }}"></script>
@endpush

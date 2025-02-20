@extends('layouts.master')

@section('css')
    <link href="{{ URL::asset('assets/plugins/sweet-alert/jquery.sweet-modal.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/sweet-alert/sweetalert.css') }}" rel="stylesheet" />
@endsection

@section('content')
<div class="container">
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <h2>Edit Wheel</h2>
    <form action="{{ route('wheel.update', $wheel->id) }}" method="POST" novalidate class="needs-validation">
        @csrf
       
        <!-- Game Selection Dropdown -->
        <div class="mb-3">
            <label for="game_id" class="form-label">Select Game</label>
            <select id="game_id" name="game_id" class="form-control" required>
                <option value="">-- Select Game --</option>
                @foreach($games as $game)
                    <option value="{{ $game->id }}" {{ $game->id == $wheel->game_id ? 'selected' : '' }}>
                        {{ $game->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Dynamic Table -->
        <table class="table" id="clips_table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Text</th>
                </tr>
            </thead>
            <tbody>
                @foreach($wheel->clips as $index => $clip)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <input type="text" name="clips[{{ $index }}][text]" class="form-control text-input"
                            value="{{ $clip->text }}" data-maxlength="{{ $clip->gameClip->text_length }}" 
                            maxlength="{{ $clip->gameClip->text_length }}" required>
                        <input type="hidden" name="clips[{{ $index }}][id]" value="{{ $clip->game_clip_id }}">
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <button type="submit" class="btn btn-primary">Update Wheel</button>
    </form>
</div>
@endsection

@section('js')
<script src="{{ URL::asset('assets/plugins/sweet-alert/jquery.sweet-modal.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/sweet-alert/sweetalert.min.js') }}"></script>
<script>
$(document).ready(function() {
    checkValidation();

    $('#game_id').change(function() {
        let gameId = $(this).val();
        
        if (gameId) {
            swal({
                title: "Are you sure?",
                text: "Changing the game will remove the existing clips data!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
                showCancelButton: true,
                confirmButtonText: "Yes, Change Game",
                cancelButtonText: "Cancel"
            }, function(willChange) {
                if (willChange) {
                    fetchClips(gameId);
                } else {
                    // Reset dropdown if user cancels
                    $('#game_id').val("{{ $wheel->game_id }}");
                }
            });
        }
    });

    function fetchClips(gameId) {
        $.ajax({
            url: "{{ route('getClipsByGame') }}",
            type: "GET",
            data: { game_id: gameId },
            success: function(response) {
                let clips = response.clips;
                let tableBody = $('#clips_table tbody');
                tableBody.empty();

                clips.forEach((clip, index) => {
                    let textLength = clip.text_length;
                    let row = `
                        <tr>
                            <td>${index + 1}</td>
                            <td>
                                <input type="text" name="clips[${index}][text]" class="form-control text-input"
                                    data-maxlength="${textLength}" maxlength="${textLength}" required>
                            </td>
                        </tr>`;
                    tableBody.append(row);
                });

                $('.text-input').on('input', function() {
                    let maxLength = $(this).data('maxlength');
                    if ($(this).val().length > maxLength) {
                        $(this).val($(this).val().substring(0, maxLength));
                    }
                });
            }
        });
    }

    function checkValidation() {
        var forms = document.getElementsByClassName('needs-validation');
        Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }
});
</script>
@endsection

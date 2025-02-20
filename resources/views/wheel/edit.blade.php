@extends('layouts.master')

@section('content')
<div class="container">
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
                            value="{{ $clip->text }}" data-maxlength="{{ $clip->text_length }}" 
                            maxlength="{{ $clip->text_length }}" required>
                        <input type="hidden" name="clips[{{ $index }}][id]" value="{{ $clip->id }}">
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
<script>
$(document).ready(function() {
    checkValidation();

    $('#game_id').change(function() {
        let gameId = $(this).val();
        
        if (gameId) {
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
        } else {
            $('#clips_table tbody').empty();
        }
    });

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

@extends('layouts.master')

@section('content')
<div class="container">
    <h2>Create Wheel</h2>
    <form action="{{ route('wheel.store') }}" method="POST" novalidate class="needs-validation">
        @csrf

        <!-- Game Selection Dropdown -->
        <div class="mb-3">
            <label for="game_id" class="form-label">Select Game</label>
            <select id="game_id" name="game_id" class="form-control" required>
                <option value="">-- Select Game --</option>
                @foreach($games as $game)
                    <option value="{{ $game->id }}">{{ $game->name }}</option>
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
                <!-- Rows will be appended here -->
            </tbody>
        </table>

        <button type="submit" class="btn btn-primary">Save Wheel</button>
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
                url: "{{ route('getClipsByGame') }}", // Route to fetch clips
                type: "GET",
                data: { game_id: gameId },
                success: function(response) {
                    let clips = response.clips;
                    let tableBody = $('#clips_table tbody');
                    tableBody.empty(); // Clear existing rows

                    clips.forEach((clip, index) => {
                        let textLength = clip.text_length; // Fetch text_length from backend
                        let row = `
                            <tr>
                                <td>${index + 1}</td>
                                <td>
                                    <input type="text" name="clips[${index}][text]" class="form-control text-input"
                                        data-maxlength="${textLength}" maxlength="${textLength}" required="">
                                </td>
                            </tr>`;
                        tableBody.append(row);
                    });

                    // Enforce text length restriction
                    $('.text-input').on('input', function() {
                        let maxLength = $(this).data('maxlength');
                        if ($(this).val().length > maxLength) {
                            $(this).val($(this).val().substring(0, maxLength));
                        }
                    });
                }
            });
        } else {
            $('#clips_table tbody').empty(); // Clear table if no game selected
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

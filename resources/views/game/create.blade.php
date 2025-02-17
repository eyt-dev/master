@extends('layouts.master')

@section('content')
<div class="container">
    <h1>Create Game</h1>
    <form action="{{ route('game.store') }}" method="POST" novalidate="" class="needs-validation">
        @csrf

        <!-- Game Information -->
        <div class="form-group">
            <label for="name">Game Name</label>
            <input type="text" name="name" id="name" class="form-control" required="">
        </div>

        <div class="form-group">
            <label for="type">Game Type</label>
            <select name="type" id="type" class="form-control" required="">
                <option value="Flixable">Flixable</option>
                <option value="textable">textable</option>
                <option value="standard">standard</option>
            </select>
        </div>

        <div class="form-group">
            <label for="visibility">Visibility</label>
            <select name="visibility" id="visibility" class="form-control" required="">
                <option value="private">private</option>
                <option value="global">global</option>
            </select>
        </div>

        <div class="form-group">
            <label for="display">Display</label>
            <select name="display" id="display" class="form-control" required="">
                <option value="color">Color</option>
                <option value="image">Image</option>
            </select>
        </div>

        <div class="form-group">
            <label for="clips_count">Number of Clips</label>
            <select name="clips_count" id="clips_count" class="form-control" required="">
                @for($i = 1; $i <= 30; $i++)
                    <option value="{{ $i }}">{{ $i }}</option>
                @endfor
            </select>
        </div>

        <div class="form-group">
            <label for="created_by">Created By (User ID)</label>
            <input type="number" name="created_by" id="created_by" class="form-control" required="">
        </div>

        <hr>

        <!-- Clips Table -->
        <h3>Clips</h3>
        <table class="table" id="clips_table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Text Length</th>
                    <th>Text Orientation</th>
                    <th>Color</th>
                    <th>Image</th>
                </tr>
            </thead>
            <tbody>
                <!-- Clip rows will be generated here -->
            </tbody>
        </table>

        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
@endsection

@section('js')
<script>
$(document).ready(function(){
    checkValidation();
    function generateClipRows(count){
        let tbody = $('#clips_table tbody');
        tbody.empty();
        for(let i = 0; i < count; i++){
            let row = `<tr>
                <td>${i+1}</td>
                <td>
                    <select name="text_length[]" class="form-control" required="">
                        @for($j = 1; $j <= 5; $j++)
                            <option value="{{ $j }}">{{ $j }}</option>
                        @endfor
                    </select>
                </td>
                <td>
                    <select name="text_orientation[]" class="form-control" required="">
                        <option value="H">H</option>
                        <option value="V">V</option>
                    </select>
                </td>
                <td>
                    <input type="text" name="color[]" class="form-control" placeholder="Color" />
                </td>
                <td>
                    <input type="text" name="image[]" class="form-control" placeholder="Image" />
                </td>
            </tr>`;
            tbody.append(row);
        }
    }

    // Generate rows for the initial clips count selection.
    generateClipRows($('#clips_count').val());

    // Regenerate clip rows when the number of clips is changed.
    $('#clips_count').on('change', function(){
        let count = $(this).val();
        generateClipRows(count);
        checkValidation();
    });

    function checkValidation() {
        var forms = document.getElementsByClassName('needs-validation');
        var validation = Array.prototype.filter.call(forms, function(form) {
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

@extends('layouts.master')

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

    <h1>Create Game</h1>
    <form action="{{ route('game.store') }}" method="POST" enctype="multipart/form-data" novalidate class="needs-validation">
        @csrf

        <!-- Game Information -->
        <div class="form-group">
            <label for="name">Game Name</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="type">Game Type</label>
            <select name="type" id="type" class="form-control" required>
                <option value="Flixable">Flixable</option>
                <option value="textable">Textable</option>
                <option value="standard">Standard</option>
            </select>
        </div>

        <div class="form-group">
            <label for="visibility">Visibility</label>
            <select name="visibility" id="visibility" class="form-control" required>
                <option value="private">Private</option>
                <option value="global">Global</option>
            </select>
        </div>

        <div class="form-group">
            <label for="display">Display</label>
            <select name="display" id="display" class="form-control" required>
                <option value="color">Color</option>
                <option value="image">Image</option>
            </select>
        </div>

        <!-- This field appears only when type is "standard" -->
        <div class="form-group" id="standard_image_upload" style="display:none;">
            <label for="standard_image">Standard Image Upload</label>
            <input type="file" name="standard_image" id="standard_image" class="form-control">
        </div>

        <div class="form-group" id="clips-count-group">
            <label for="clips_count">Number of Clips</label>
            <select name="clips_count" id="clips_count" class="form-control" required>
                @for($i = 1; $i <= 30; $i++)
                    <option value="{{ $i }}">{{ $i }}</option>
                @endfor
            </select>
        </div>

        {{-- <div class="form-group">
            <label for="created_by">Created By (User ID)</label>
            <input type="number" name="created_by" id="created_by" class="form-control" required>
        </div> --}}

        <hr>

        <!-- Clips Table -->
        <div id="clips_container">
            <h3>Clips</h3>
            <table class="table" id="clips_table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Text Length</th>
                        <th>Text Orientation</th>
                        <th class="clip-color">Color</th>
                        <th class="clip-image">Image</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Clip rows will be generated here -->
                </tbody>
            </table>
        </div>

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
        if(count){
            for(let i = 0; i < count; i++){
                let row = `<tr>
                    <td>${i+1}</td>
                    <td>
                        <select name="text_length[]" class="form-control" required>
                            @for($j = 1; $j <= 30; $j++)
                                <option value="{{ $j }}">{{ $j }}</option>
                            @endfor
                        </select>
                    </td>
                    <td>
                        <select name="text_orientation[]" class="form-control" required>
                            <option value="H">H</option>
                            <option value="V">V</option>
                        </select>
                    </td>
                    <td class="clip-color">
                        <input type="color" name="color[]" class="form-control" placeholder="Color">
                    </td>
                    <td class="clip-image">
                        <input type="file" name="image[]" class="form-control" placeholder="Image">
                    </td>
                </tr>`;
                tbody.append(row);
            }
        }
        adjustClipTableColumns();
    }

    // Generate rows for the initial clips count selection.
    generateClipRows($('#clips_count').val());

    // Regenerate clip rows when the number of clips is changed.
    $('#clips_count').on('change', function(){
        let count = $(this).val();
        generateClipRows(count);
        checkValidation();
    });

    // When the game type changes, adjust the UI elements accordingly.
    $('#type').on('change', function(){
        let typeVal = $(this).val().toLowerCase();
        if(typeVal === 'standard'){
            $('#display').val('image').prop('disabled', true);
            $('#clips-count-group, #clips_container').hide();
            $('#standard_image_upload').show();
            $('#display-container').show(); // Ensure the display dropdown is visible
            generateClipRows(0);
        } else if(typeVal === 'textable'){
            $('#display-container').hide(); // Hide display dropdown
            $('#clips-count-group, #clips_container').show();
            $('#standard_image_upload').show();
            generateClipRows($('#clips_count').val());
        } else { // Flixable
            $('#display').prop('disabled', false);
            $('#clips-count-group, #clips_container').show();
            $('#standard_image_upload').hide();
            $('#display-container').show();
            generateClipRows($('#clips_count').val());
        }
    });

    // When display dropdown changes (if enabled), adjust columns.
    $('#display').on('change', function(){
        adjustClipTableColumns();
    });

    // Function to adjust column visibility based on game type and display value.
    function adjustClipTableColumns(){
        let typeVal = $('#type').val().toLowerCase();
        let displayVal = $('#display').val();

        $('#clips_table tbody tr').each(function(){
            if(typeVal === 'textable'){
                $('.clip-color, .clip-image').hide();
            } else {
                if(displayVal === 'image'){
                    $('.clip-color').hide();
                    $('.clip-image').show();
                } else if(displayVal === 'color'){
                    $('.clip-color').show();
                    $('.clip-image').hide();
                }
            }
        });
    }

    // Initialize state based on default values.
    $('#type').trigger('change');
    $('#display').trigger('change');

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

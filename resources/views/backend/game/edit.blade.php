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
    <h1>Edit Game</h1>
    <form action="{{ route('game.update', ['site' => $siteSlug, 'game' => $game->id]) }}" method="POST" enctype="multipart/form-data" novalidate class="needs-validation">
        @csrf
        {{-- Uncomment if you are using PUT method via hidden _method field --}}
        {{-- @method('PUT') --}}

        <!-- Game Information -->
        <div class="form-group">
            <label for="name">Game Name</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $game->name) }}" required>
        </div>

        <div class="form-group">
            <label for="type">Game Type</label>
            <select name="type" id="type" class="form-control" required>
                <option value="Flixable" {{ $game->type == 'Flixable' ? 'selected' : '' }}>Flixable</option>
                <option value="textable" {{ $game->type == 'textable' ? 'selected' : '' }}>Textable</option>
                <option value="standard" {{ $game->type == 'standard' ? 'selected' : '' }}>Standard</option>
            </select>
        </div>

        <div class="form-group">
            <label for="visibility">Visibility</label>
            <select name="visibility" id="visibility" class="form-control" required>
                <!-- Assuming stored visibility: global = true, private = false -->
                <option value="private" {{ !$game->visibility ? 'selected' : '' }}>Private</option>
                <option value="global" {{ $game->visibility ? 'selected' : '' }}>Global</option>
            </select>
        </div>

        <div class="form-group" id="display-container">
            <label for="display">Display</label>
            <select name="display" id="display" class="form-control" required>
                <option value="color" {{ $game->display == 'color' ? 'selected' : '' }}>Color</option>
                <option value="image" {{ $game->display == 'image' ? 'selected' : '' }}>Image</option>
            </select>
        </div>

        <!-- Standard image upload field shown only when type is standard -->
        <div class="form-group" id="standard_image_upload" style="display: none;">
            <label for="standard_image">Standard Image Upload</label>
            <input type="file" name="standard_image" id="standard_image" class="form-control">
            @if(isset($game->image))
                <img src="{{ asset('storage/' . $game->image) }}" alt="Standard Image" style="max-width: 200px; margin-top:10px;">
            @endif
        </div>

        <div class="form-group" id="clips-count-group">
            <label for="clips_count">Number of Clips</label>
            <select name="clips_count" id="clips_count" class="form-control" required>
                @for($i = 1; $i <= 30; $i++)
                    <option value="{{ $i }}" {{ count($game->clipData) == $i ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
            </select>
        </div>

        {{-- <div class="form-group">
            <label for="created_by">Created By (User ID)</label>
            <input type="number" name="created_by" id="created_by" class="form-control" value="{{ old('created_by', $game->created_by) }}" required>
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
                    @if($game->clipData)
                        @foreach($game->clipData as $index => $clip)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <select name="text_length[]" class="form-control" required>
                                        @for($j = 1; $j <= 30; $j++)
                                            <option value="{{ $j }}" {{ $clip->text_length == $j ? 'selected' : '' }}>{{ $j }}</option>
                                        @endfor
                                    </select>
                                </td>
                                <td>
                                    <select name="text_orientation[]" class="form-control" required>
                                        <option value="H" {{ $clip->text_orientation == 'H' ? 'selected' : '' }}>H</option>
                                        <option value="V" {{ $clip->text_orientation == 'V' ? 'selected' : '' }}>V</option>
                                    </select>
                                </td>
                                <td class="clip-color">
                                    <input type="color" name="color[]" class="form-control" placeholder="Color" value="{{ $clip->color }}">
                                </td>
                                <td class="clip-image">
                                    <input type="file" name="image[]" class="form-control" placeholder="Image">
                                    @if($clip->image) 
                                        <img src="{{ asset('storage/' . $clip->image) }}" alt="Clip Image" style="max-width: 100px; margin-top:10px;">
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
@endsection

@section('js')
<script>
$(document).ready(function(){

// When the clips_count dropdown changes, adjust the number of clip rows.
$('#clips_count').on('change', function(){
    let newCount = parseInt($(this).val());
    let tbody = $('#clips_table tbody');
    let existingCount = tbody.find('tr').length;

    // Append new rows if needed
    if(newCount > existingCount){
        for(let i = existingCount; i < newCount; i++){
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
                    <input type="color" name="color[]" class="form-control">
                </td>
                <td class="clip-image">
                    <input type="file" name="image[]" class="form-control">
                </td>
            </tr>`;
            tbody.append(row);
        }
    }
    // Remove extra rows if needed
    else if(newCount < existingCount){
        tbody.find('tr').slice(newCount).remove();
    }

    // Update row numbering
    tbody.find('tr').each(function(index){
        $(this).find('td:first').text(index + 1);
    });

    adjustClipTableColumns();
});

// When the game type changes, adjust the form accordingly.
$('#type').on('change', function(){
    let typeVal = $(this).val().toLowerCase();
    if(typeVal === 'standard'){
        $('#display').val('image').prop('disabled', true);
        $('#clips-count-group, #clips_container').hide();
        $('#standard_image_upload').show();
        // $('#display-container').show(); // Ensure display dropdown is visible
    } else if(typeVal === 'textable'){
        $('#display').val('image').prop('disabled', true);
        // $('#display-container').show(); // Hide display dropdown
        $('#clips-count-group, #clips_container').show();
        $('#standard_image_upload').show();
        if($('#clips_table tbody tr').length === 0){
            $('#clips_count').trigger('change');
        }
    } else { // Flixable
        $('#display').prop('disabled', false);
        $('#clips-count-group, #clips_container').show();
        $('#standard_image_upload').hide();
        // $('#display-container').show();
        if($('#clips_table tbody tr').length === 0){
            $('#clips_count').trigger('change');
        }
    }
    adjustClipTableColumns();
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

// Basic client-side validation
(function() {
  'use strict';
  window.addEventListener('load', function() {
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
  }, false);
})();
});

</script>
@endsection


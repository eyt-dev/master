@extends('layouts.master')

@section('content')
<div class="container">
    <h1>Edit Game</h1>
    <form action="{{ route('game.update', $game->id) }}" method="POST" novalidate class="needs-validation">
        @csrf
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
                <option value="textable" {{ $game->type == 'textable' ? 'selected' : '' }}>textable</option>
                <option value="standard" {{ $game->type == 'standard' ? 'selected' : '' }}>standard</option>
            </select>
        </div>

        <div class="form-group">
            <label for="visibility">Visibility</label>
            <select name="visibility" id="visibility" class="form-control" required>
                <!-- Assuming stored visibility: global = true, private = false -->
                <option value="private" {{ !$game->visibility ? 'selected' : '' }}>private</option>
                <option value="global" {{ $game->visibility ? 'selected' : '' }}>global</option>
            </select>
        </div>

        <div class="form-group">
            <label for="display">Display</label>
            <select name="display" id="display" class="form-control" required>
                <option value="color" {{ $game->display == 'color' ? 'selected' : '' }}>Color</option>
                <option value="image" {{ $game->display == 'image' ? 'selected' : '' }}>Image</option>
            </select>
        </div>

        <div class="form-group">
            <label for="clips_count">Number of Clips</label>
            <select name="clips_count" id="clips_count" class="form-control" required>
                @for($i = 1; $i <= 30; $i++)
                    <option value="{{ $i }}" {{ $game->clips == $i ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
            </select>
        </div>

        <div class="form-group">
            <label for="created_by">Created By (User ID)</label>
            <input type="number" name="created_by" id="created_by" class="form-control" value="{{ old('created_by', $game->created_by) }}" required>
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
                @if($game->clipData)
                    @foreach($game->clipData as $index => $clip)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <select name="text_length[]" class="form-control" required>
                                    @for($j = 1; $j <= 5; $j++)
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
                            <td>
                                <input type="text" name="color[]" class="form-control" placeholder="Color" value="{{ $clip->color }}" />
                            </td>
                            <td>
                                <input type="text" name="image[]" class="form-control" placeholder="Image" value="{{ $clip->image }}" />
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>

        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
@endsection

@section('js')
<script>
$(document).ready(function(){
    // Generate clip rows when the clips_count changes.
    // In the edit page, you might choose to simply let the user add or remove rows.
    $('#clips_count').on('change', function(){
        let count = $(this).val();
        // Remove current rows if the new count is different from the current rows count.
        // For simplicity, we clear and generate blank rows.
        let tbody = $('#clips_table tbody');
        tbody.empty();
        for(let i = 0; i < count; i++){
            let row = `<tr>
                <td>${i+1}</td>
                <td>
                    <select name="text_length[]" class="form-control" required>
                        @for($j = 1; $j <= 5; $j++)
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
                <td>
                    <input type="text" name="color[]" class="form-control" placeholder="Color" />
                </td>
                <td>
                    <input type="text" name="image[]" class="form-control" placeholder="Image" />
                </td>
            </tr>`;
            tbody.append(row);
        }
    });

    // Basic client-side validation (same as in create)
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

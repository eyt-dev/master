<form id="ajax_module_form" method="POST">
    @csrf
    <div class="form-group">
        <label for="module_name">Module Name</label>
        <input type="text" class="form-control" id="module_name" name="name" placeholder="Enter module name" required />
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>

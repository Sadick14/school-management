<!-- Bulk Enroll Modal -->
<div id="bulkEnrollModal" class="modal fade" role="dialog">
    <div class="modal-dialog" style="width: 600px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Bulk Enroll Students by Name</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Select Class <span class="text-danger">*</span></label>
                    <select id="bulkEnrollClass" class="form-control select2">
                        <option value="">Choose a class...</option>
                        @foreach($classes as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Enter Student Names <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="text" id="studentNameInput" class="form-control" placeholder="Type student name and press Enter or click Add">
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-default" id="addNameBtn">
                                <i class="fa fa-plus"></i> Add
                            </button>
                        </span>
                    </div>
                </div>

                <div class="form-group">
                    <label>Students to Enroll (<span id="studentCount">0</span>)</label>
                    <div id="studentNamesList" class="well" style="min-height: 150px; max-height: 250px; overflow-y: auto;">
                        <p class="text-muted">No students added yet</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="bulkEnrollBtn" disabled>
                    <i class="fa fa-check"></i> Enroll All
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .student-name-badge {
        display: inline-block;
        background: #3c8dbc;
        color: white;
        padding: 8px 12px;
        border-radius: 3px;
        margin: 5px;
        font-size: 13px;
    }
    .student-name-badge .remove {
        cursor: pointer;
        margin-left: 8px;
        font-weight: bold;
    }
    .student-name-badge .remove:hover {
        color: #fff;
    }
</style>

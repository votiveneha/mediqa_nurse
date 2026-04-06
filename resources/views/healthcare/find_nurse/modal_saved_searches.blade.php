<style>
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.4);
    display: flex;
    justify-content: center; /* centers horizontally */
    align-items: center;     /* centers vertically */
    z-index: 9999;
}
.modal-content {
    background: #fff;
    padding: 20px 30px;
    border-radius: 6px;
    width: 400px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    font-weight: bold;
    color: #000;
}

.close-modal {
    background: none;
    border: none;
    font-size: 20px;
    cursor: pointer;
}

.modal-alert {
    background: #fff8e1;
    border: 1px solid #ffe082;
    padding: 10px;
    margin: 15px 0;
    font-size: 14px;
    color: #000;
}

.alert-icon {
    font-weight: bold;
    margin-right: 8px;
    color: #1976d2;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    font-weight: bold;
    color: #000;
}

.form-tip {
    font-size: 12px;
    color: #888;
}

.modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

.btn-cancel {
    background: #fff;
    border: 1px solid #000;
    padding: 6px 12px;
    cursor: pointer;
}

.btn-save {
    background: #000;
    color: #fff;
    border: none;
    padding: 6px 12px;
    cursor: pointer;
}

</style>
<div class="modal-overlay" id="save-search-modal" style="display:none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modal-title">Save Search</h3>
            <button type="button" class="close-modal btn-cancel"  >&times;</button>
        </div>

        <!-- Alert box -->
        <div class="modal-alert">
            <span class="alert-icon">!</span>
            <p>
                You've modified filters from the job criteria.<br>
                Saving will create a new custom search.<br>
                The original job tab will remain unchanged.
            </p>
        </div>

        <form id="add_saved_searches" method="POST" onsubmit="return add_saved_searches()">
            @csrf

            <!-- Search Name -->
            <div class="form-group">
                <label for="search-name">Search Name</label>
                <input type="text" id="search-name" name="search_name"
                       placeholder="ICU RN – Night Shift Only">
                       <span id='reqsearch-name'></span>
                <small class="form-tip">
                    Tip: Use a name that reflects your refinements (e.g., "ICU RN – Night Shift Only")
                </small>
                <span id="reqsearch-name" class="reqError text-danger"></span>
            </div>
            <!-- Actions -->
            <div class="modal-actions">
                <button type="button" class="btn-cancel">Cancel</button>
                <button type="submit" id="submitSavedSearches" class="btn-save">Save as New</button>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).on('click', '.saved-add-search', function(e) {
        e.preventDefault();
        $('#save-search-modal').fadeIn(); // show modal
    });

   $('.btn-cancel').on('click', function() {
     $('#save-search-modal').fadeOut(200);
   });
</script>
<script>
    function add_saved_searches() {

    let name = $('#search-name').val().trim();

    if (!name) {
        $('#reqsearch-name').text('Search name is required');
        return false;
    }

    $('#reqsearch-name').text('');

    $.ajax({
        type: "POST",
        url: "{{ url('/healthcare-facilities/hFaddSavedSearches') }}",

        data: {
            search_name: name,
            _token: "{{ csrf_token() }}"
        },

        beforeSend: function () {
            $('#submitSavedSearches').prop('disabled', true);
            $('#submitSavedSearches').text('Processing...');
        },

        success: function (res) {
            $('#submitSavedSearches').prop('disabled', false);
            $('#submitSavedSearches').text('Save as New');
            if (res.status == 1) {
                // ✅ Add new tab dynamically
                addSearchToUI(res.id, name);
                // ✅ Close modal
                $('#save-search-modal').fadeOut();
                // ✅ Reset form
                $('#add_saved_searches')[0].reset();
                showToast('Saved search created.');
            } else {
                showToast(res.message);
            }
        },
        error: function (err) {

            $('#submitSavedSearches').prop('disabled', false);
            $('#submitSavedSearches').text('Save as New');

            if (err.responseJSON && err.responseJSON.errors) {

                let errors = err.responseJSON.errors;

                if (errors.search_name) {
                    $('#reqsearch-name').text(errors.search_name[0]);
                }
            }
        }
    });
    return false;
}
</script>
<script>
let timer;
    $('#search-name').on('keyup', function () {

        clearTimeout(timer);

        let name = $(this).val().trim();

        if (name.length < 3) {
            $('#reqsearch-name').text('');
            return;
        }

        timer = setTimeout(function () {

            $.ajax({
                type: "POST",
                url: "{{ url('/healthcare-facilities/check-search-name') }}",
                data: {
                    search_name: name,
                    id: typeof currentSearchId !== 'undefined' ? currentSearchId : '',
                    _token: "{{ csrf_token() }}"
                },
                success: function (res) {
                    if (res.exists) {
                        $('#reqsearch-name').text('This search name already exists');
                    } else {
                        $('#reqsearch-name').text('');
                    }
                }
            });

        }, 400); // delay
    });
</script>
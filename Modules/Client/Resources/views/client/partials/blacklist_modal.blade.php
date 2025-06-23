<!-- Blacklist Modal -->
<div class="modal fade" id="blacklistModal" tabindex="-1" aria-labelledby="blacklistModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="blacklistModalLabel">Blacklist Client</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ url('client/' . $client->id . '/blacklist') }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="reason">Reason</label>
                        <textarea class="form-control" id="reason" name="reason" required></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label for="status">Status</label>
                        <select class="form-control" id="status_level" name="status_level">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-warning">Blacklist</button>
                </div>
            </form>
        </div>
    </div>
</div>

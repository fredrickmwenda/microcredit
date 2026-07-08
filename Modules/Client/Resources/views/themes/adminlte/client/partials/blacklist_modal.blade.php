<div class="modal fade" id="blacklistModal" tabindex="-1" role="dialog" aria-labelledby="blacklistModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form method="POST" action="{{ url('client/' . $client->id . '/blacklist') }}">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="blacklistModalLabel">Blacklist Client</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="reason">Reason for Blacklisting</label>
            <textarea class="form-control" name="reason" id="reason" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger">Blacklist</button>
        </div>
      </div>
    </form>
  </div>
</div>

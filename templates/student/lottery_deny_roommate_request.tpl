<h2>Deny Roommate Request</h2>

<div class="col-md-12">
  <div class="row">
    <p class="col-md-8">
      You are about to deny a roommate request from <strong>{REQUESTOR}</strong>.
    </p>
  </div>

  <div class="row">
    <p class="col-md-8">
      <strong>This cannot be un-done</strong>. There is no guarantee that you will
      receive another invition for on-campus housing. To continue, in the text box
      below, enter the security words seen in the image.
    </p>
  </div>

  {START_FORM}
  <div class="row">
    <div class="col-md-8">
      {CAPTCHA}
    </div>
  </div>

  <p>

  </p>

  <div class="row">
    <div class="col-md-2">
      <button type="submit" class="btn btn-primary btn-lg">
        Deny Request
      </button>
    </div>
  </div>

  {END_FORM}
</div>

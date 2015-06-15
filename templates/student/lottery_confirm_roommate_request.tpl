<h2>Confirm Roommate Request</h2>

<div class="col-md-12">
  <div class="row">
    <p class="col-md-8">
      To confirm that you would like to be roommates with <b>{REQUESTOR}</b> and the other possible roommates listed below in <b>{HALL_ROOM}</b> please type the words shown below in the text box provided and click the confirm button. Please be aware that empty beds will be made available for other students.
    </p>
  </div>

  <div class="row">
    <label class="col-md-3">
      Bedroom
    </label>
    <label class="col-md-3 col-md-offset-1">
      Roommate
    </label>
  </div>

  <!-- BEGIN beds -->
  <div class="row">
    <label class="col-md-3">
      {BEDROOM_LETTER}
    </label>
    <p class="col-md-3 col-md-offset-1">
      {TEXT}
    </p>
  </div>
  <!-- END beds -->

  {START_FORM}

  <div class="row">
    <label class="col-md-2">
      Meal plan:
    </label>
    <p class="col-md-2 col-md-offset-1">
      {MEAL_PLAN}
    </p>
  </div>

  <p>{CAPTCHA}</p>

  <div class="row">
    <div class="col-md-2">
      <button type="submit" class="btn btn-lg btn-success">
        Confirm Roommate
      </button>
    </div>
  </div>

  {END_FORM}

</div>

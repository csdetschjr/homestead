<h2>Confirm Room & Roommates</h2>

<div class="col-md-12">
  <div class="row">
    <p class="col-md-9">
      Please confirm your room and roommate choices below.
    </p>
  </div>

  <div class="row">
    <p class="col-md-9">
      You will be assigned to:
    </p>
  </div>

  <div class="row">
    <div class="col-md-9">
      <h4>
        {ROOM}
      </h4>
    </div>
  </div>

  <div class="row">
    <p class="col-md-9">
      The roommate(s) you have chosen will be sent an email to confirm your request. If confirmed, the people in your room will be:
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

  <div class="row">
    <label class="col-md-2">
      Meal plan:
    </label>
    <p class="col-md-2">
      {MEAL_PLAN}
    </p>
  </div>

  {START_FORM}
  <div class="row">
    <p class="col-md-9">
      To confirm your room and roommate selections please type the words shown in
      the image below in the text field provided. (If you cannot read the words,
      click the refresh button under the image to get new words.)
    </p>
  </div>
  {CAPTCHA_IMAGE}
  <div class="row">
    <p></p>
    <div class="col-md-2">
      <button type="submit" class="btn btn-lg btn-success">
        Confirm Room & Roommates
      </button>
    </div>
  </div>
  {END_FORM}

</div>

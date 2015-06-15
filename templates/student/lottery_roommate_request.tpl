<h2>Confirm Roommate Request</h2>

<div class="col-md-12">
  <div class="row">
    <p class="col-md-9">
      {REQUESTOR} has requested you as a roommate in <b>{HALL_ROOM}</b>. The
      other assigned roommates, requested roommates, and vacant beds are listed
      below for your consideration. Please be aware that empty beds will be made
      available to other students.
    </p>
  </div>

  <div class="row">
    <label class="col-md-2">
      Bedroom
    </label>
    <label class="col-md-2">
      Roommate
    </label>
  </div>

  <!-- BEGIN beds -->
    <div class="row">
      <label class="col-md-2">
        {BEDROOM_LETTER}
      </label>
      <p class="col-md-2">
        {TEXT}
      </p>
    </div>
  <!-- END beds -->


  {START_FORM}

  <div class="row">
      <p class="col-md-9">
        Please choose a meal plan. <b>Note: </b>Most residence halls require you to
        choose a meal plan. If your chosen residence hall does not require a meal
        plan, then a 'None' option will be available in drop down box below.
      </p>
  </div>

  <div class="row">
      <label class="col-md-2">
        Meal plan:
      </label>
      <div class="col-md-2">
        {MEAL_PLAN}
      </div>
  </div>

  <div class="row">
      <p class="col-md-9">
        To confirm your choices, please continue to the next page by clicking the
        continue button below.
      </p>
  </div>

  <div class="row">
    <div class="col-md-2">
      <a href="{REJECT}" class="btn btn-lg btn-danger pull-left">
        Deny Roommate
        <i class="fa fa-thumbs-down"></i>
      </a>
    </div>
    <div class="col-md-2 col-md-offset-1">
      <button type="submit" class="btn btn-lg btn-success pull-right">
        <i class="fa fa-thumbs-up"></i>
        Accept Roommate
      </button>
    </div>
  </div>

  {END_FORM}

</div>

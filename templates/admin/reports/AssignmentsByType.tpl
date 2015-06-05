<h2>{NAME} <small>{TERM}</small></h2>

<div class="col-md-6">
  <p>
    Executed on: {EXEC_DATE} by {EXEC_USER}
  </p>
</div>

<div class="col-md-8">
  <table class="table table-striped table-hover">
    <tr>
      <th>Assignment Reason</th>
      <th># Assignments</th>
    </tr>

    <!-- BEGIN TABLE_ROWS -->
    <tr>
      <td>{REASON}</td>
      <td>{COUNT}</td>
    </tr>
    <!-- END TABLE_ROWS -->

    <tr>
      <td><strong>Total:</strong></td>
      <td><strong>{TOTAL_ASSIGNMENTS}</strong></td>
    </tr>
  </table>
</div>

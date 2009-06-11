<div class="hms">
  <div class="box">
    <div class="{TITLE_CLASS}"><h1>{TITLE}</h1></div>
    <div class="box-content">
        <!-- BEGIN error_msg -->
        <span class="error">{ERROR_MSG}</span><br />
        <!-- END error_msg -->
        
        <!-- BEGIN success_msg -->
        <span class="success">{SUCCESS_MSG}</span><br />
        <!-- END success_msg -->
        
        {MESSAGE}<br /><br />
        {START_FORM}
        <table>
            <tr>
                <th align="left">{RESIDENCE_HALL_LABEL}</th>
                <td>{RESIDENCE_HALL}</td>
            </tr>
            <tr>
                <th align="left">{FLOOR_LABEL}</th>
                <td>{FLOOR}</td>
            </tr>
        </table>
        <br />
        {SUBMIT_BUTTON}
        {END_FORM}
    </div>
  </div>
</div>

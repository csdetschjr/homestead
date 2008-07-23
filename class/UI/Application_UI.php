<?php

class Application_UI{

    function show_housing_application($error_msg = NULL)
    {
        # Try to load the user's application, in case it already exists
        $application = new HMS_Application($_SESSION['asu_username'], $_SESSION['application_term']);
        
        PHPWS_Core::initCoreClass('Form.php');
        $form = &new PHPWS_Form();

        $tpl = array();

        /*******************
         * Agreed to terms *
         *******************/
        # If the 'agreed_to_terms' flag was passed in the request, then use it.
        # Otherwise look for it from an existing application.
        if(isset($_REQUEST['agreed_to_terms'])){
            $form->addHidden('agreed_to_terms', $_REQUEST['agreed_to_terms']);
        }else if(isset($application->agreed_to_terms)){
            $form->addHidden('agreed_to_terms',$application->agreed_to_terms);
        }else{
            $form->addHidden('agreed_to_terms', 0);
        }

        /****************
         * Display Info *
         ****************/
        $tpl['STUDENT_NAME']    = HMS_SOAP::get_full_name($_SESSION['asu_username']);
        $tpl['GENDER']          = (HMS_SOAP::get_gender($_SESSION['asu_username'],TRUE) == FEMALE) ? FEMALE_DESC : MALE_DESC;
        $tpl['ENTRY_TERM']      = HMS_Term::term_to_text($_SESSION['application_term'], TRUE);
        $tpl['CLASSIFICATION_FOR_TERM_LBL'] = HMS_Util::formatClass(HMS_SOAP::get_student_class($_SESSION['asu_username'], $_SESSION['application_term']));
        $tpl['STUDENT_STATUS_LBL'] = HMS_Util::formatType(HMS_SOAP::get_student_type($_SESSION['asu_username'], $_SESSION['application_term']));
        
        /***************
         * Meal option *
         ***************/
        # Don't show *low* meal option to freshmen
        if(HMS_SOAP::get_student_type($_SESSION['asu_username'], $_SESSION['application_term']) != 'F'){
            $form->addDropBox('meal_option', array(BANNER_MEAL_LOW=>_('Low'),
                                                   BANNER_MEAL_STD=>_('Standard'),
                                                   BANNER_MEAL_HIGH=>_('High'),
                                                   BANNER_MEAL_SUPER=>_('Super')));
        }else{
            $form->addDropBox('meal_option', array(BANNER_MEAL_STD=>_('Standard'),
                                                   BANNER_MEAL_HIGH=>_('High'),
                                                   BANNER_MEAL_SUPER=>_('Super')));
        }
        
        if(isset($_REQUEST['meal_option'])){
            $form->setMatch('meal_option',$_REQUEST['meal_option']);
        }elseif(isset($application->meal_option)){
            $form->setMatch('meal_option',$application->meal_option);
        }else{
            $form->setMatch('meal_option', BANNER_MEAL_STD);
        }
        
        /*************
         * Lifestyle *
         *************/
        # TODO: get rid of the magic numbers!!!
        $form->addDropBox('lifestyle_option', array('1'=>_('Single Gender Building'),
                                                    '2'=>_('Co-Ed Building')));
        if(isset($_REQUEST['lifestyle_option'])){
            $form->setMatch('lifestyle_option',$_REQUEST['lifestyle_option']);
        }else if(isset($application->lifestyle_option)){
            $form->setMatch('lifestyle_option',$application->lifestyle_option);
        }else{
            $form->setMatch('lifestyle_option', '1');
        }

        /************
         * Bed time *
         ************/
        # TODO: magic numbers
        $form->addDropBox('preferred_bedtime', array('1'=>_('Early'),
                                                     '2'=>_('Late')));
        if(isset($_REQUEST['preferred_bedtime'])){
            $form->setMatch('preferred_bedtime',$_REQUEST['preferred_bedtime']);
        }else if(isset($application->preferred_bedtime)){
            $form->setMatch('preferred_bedtime',$application->preferred_bedtime);
        }else{
            $form->setMatch('preferred_bedtime', '1');
        }

        /******************
         * Room condition *
         ******************/
        #TODO: magic numbers
        $form->addDropBox('room_condition', array('1'=>_('Neat'),
                                                  '2'=>_('Cluttered')));
        if(isset($_REQUEST['room_condition'])){
            $form->setMatch('room_condition',$_REQUEST['room_condition']);
        }else if(isset($application->room_condition)){
            $form->setMatch('room_condition',$application->room_condition);
        }else{
            $form->setMatch('room_condition', '1');
        }

        /*****************
         * Special needs *
         *****************/
        $tpl['SPECIAL_NEEDS_TEXT'] = ''; // setting this template variable to anything causes the special needs text to be displayed
        $form->addCheck('special_need', array('special_need'));
        $form->setLabel('special_need', array('Yes, I require special needs housing.'));

        if(isset($_REQUEST['special_need'])){
            $form->setMatch('special_need', $_REQUEST['special_need']);
        }else if($application->physical_disability == 1 || 
                 $application->psych_disability == 1 ||
                 $application->medical_need == 1||
                 $application->gender_need == 1){
            $form->setMatch('special_need', 'special_need');
        }

        /*******
         * RLC *
         *******/
        if(HMS_Entry_Term::get_entry_semester($_SESSION['asu_username']) == TERM_FALL && HMS_RLC_Application::check_for_application($_SESSION['asu_username'], $_SESSION['application_term']) == FALSE){
            $form->addRadio('rlc_interest', array(0, 1));
            $form->setLabel('rlc_interest', array(_("No"), _("Yes")));
            if(isset($_REQUEST['rlc_interest'])){
                $form->setMatch('rlc_interest',$_REQUEST['rlc_interest']);
            }else{
                $form->setMatch('rlc_interest', '0');
            }
        }else{
            $form->addHidden('rlc_interest', 0);
        }

        if(isset($_REQUEST['special_needs'])){
            $form->addHidden('special_needs', $_REQUEST['special_needs']);
        }

        $form->addSubmit('submit', _('Submit Application'));
        $form->addHidden('module', 'hms');
        $form->addHidden('type', 'student');
        $form->addHidden('op', 'submit_application');

        
        if(isset($error_msg)){
            $tpl['ERROR_MSG'] = $error_msg;
        }

        $form->mergeTemplate($tpl);
        $tpl = $form->getTemplate();

        return PHPWS_Template::process($tpl,'hms','student/student_application.tpl');
    }

    function submit_application()
    {
        # Check to see if the student has special needs
        if(isset($_REQUEST['special_need'])){
            return Application_UI::show_special_needs();
        }else{
            return Application_UI::show_application_review();
        }
    }

    function show_special_needs()
    {
        PHPWS_Core::initCoreClass('Form.php');
        $form = &new PHPWS_Form();

        $form->addCheck('special_needs', array('physical_disability','psych_disability','medical_need','gender_need'));
        $form->setLabel('special_needs', array('Physical disability', 'Psychological disability', 'Medical need', 'Transgender housing'));

        if(isset($_REQUEST['special_needs'])){
            $form->setMatch('special_needs', $_REQUEST['special_needs']);
        }

        # Carry over all the fields submitted on the first page of the application
        $form->addHidden('agreed_to_terms',$_REQUEST['agreed_to_terms']);
        $form->addHidden('meal_option',$_REQUEST['meal_option']);
        $form->addHidden('lifestyle_option',$_REQUEST['lifestyle_option']);
        $form->addHidden('preferred_bedtime',$_REQUEST['preferred_bedtime']);
        $form->addHidden('room_condition',$_REQUEST['room_condition']);
        $form->addHidden('rlc_interest',$_REQUEST['rlc_interest']);
        $form->addHidden('special_need',$_REQUEST['special_need']); // pass it on, just in case the user needs to redo their application

        $form->addHidden('module', 'hms');
        $form->addHidden('type','student');
        $form->addHidden('op','submit_application_special_needs');
        
        $form->addSubmit('submit', 'Continue');

        return PHPWS_Template::process($form->getTemplate(), 'hms', 'student/special_needs.tpl');
    }

    function show_application_review()
    {
        PHPWS_Core::initModClass('hms', 'HMS_Util.php');

        # Perform sanity checks on submitted values
        if(!is_numeric($_REQUEST['meal_option']) ||
            !is_numeric($_REQUEST['lifestyle_option']) ||
            !is_numeric($_REQUEST['preferred_bedtime']) ||
            !is_numeric($_REQUEST['room_condition']) ||
            !is_numeric($_REQUEST['rlc_interest'])){
            return Application_UI::show_housing_application('Invalid values submitted. Please try again');
        }

        $tpl = array();
        $tpl['REVIEW_MSG']      = ''; // set this to show the review message

        $tpl['STUDENT_NAME']    = HMS_SOAP::get_full_name($_SESSION['asu_username']);
        $tpl['GENDER']          = (HMS_SOAP::get_gender($_SESSION['asu_username'],TRUE) == FEMALE) ? FEMALE_DESC : MALE_DESC;
        $tpl['ENTRY_TERM']      = HMS_Term::term_to_text($_SESSION['application_term'], TRUE);
        $tpl['CLASSIFICATION_FOR_TERM_LBL'] = HMS_Util::formatClass(HMS_SOAP::get_student_class($_SESSION['asu_username'], $_SESSION['application_term']));
        $tpl['STUDENT_STATUS_LBL']          = HMS_Util::formatType(HMS_SOAP::get_student_type($_SESSION['asu_username'], $_SESSION['application_term']));

        $tpl['MEAL_OPTION']         = HMS_Util::formatMealOption($_REQUEST['meal_option']);
        $tpl['LIFESTYLE_OPTION']    = $_REQUEST['lifestyle_option'] == 1?'Single gender':'Co-ed';
        $tpl['PREFERRED_BEDTIME']   = $_REQUEST['preferred_bedtime'] == 1?'Early':'Late';
        $tpl['ROOM_CONDITION']      = $_REQUEST['room_condition'] == 1?'Clean':'Dirty';
        
        $special_needs = "";
        if(isset($_REQUEST['special_needs']['physical_disability'])){
            $special_needs = 'Physical disability<br />';
        }
        if(isset($_REQUEST['special_needs']['psych_disability'])){
            $special_needs .= 'Psychological disability<br />';
        }
        if(isset($_REQUEST['special_needs']['medical_need'])){
            $special_needs .= 'Medical need<br />';
        }
        if(isset($_REQUEST['special_needs']['gender_need'])){
            $special_needs .= 'Gender need<br />';
        }

        if($special_needs == ''){
            $special_needs = 'None';
        }
        $tpl['SPECIAL_NEEDS_RESULT'] = $special_needs;

        $tpl['RLC_INTEREST_1'] = $_REQUEST['rlc_interest'] == 0?'No':'Yes';

        $form = &new PHPWS_Form('hidden_form');
        
        # Carry over all the fields submitted on the first page of the application
        $form->addHidden('agreed_to_terms',$_REQUEST['agreed_to_terms']);
        $form->addHidden('meal_option',$_REQUEST['meal_option']);
        $form->addHidden('lifestyle_option',$_REQUEST['lifestyle_option']);
        $form->addHidden('preferred_bedtime',$_REQUEST['preferred_bedtime']);
        $form->addHidden('room_condition',$_REQUEST['room_condition']);
        $form->addHidden('rlc_interest',$_REQUEST['rlc_interest']);
        $form->addHidden('special_need',$_REQUEST['special_need']); // pass it on, just in case the user needs to redo their application
        $form->addHidden('special_needs',$_REQUEST['special_needs']);

        $form->addHidden('module', 'hms');
        $form->addHidden('type', 'student');
        $form->addHidden('op', 'submit_application_review');

        $form->addButton('redo_button', 'Modify application');
        $form->setExtra('redo_button', 'onClick="document.getElementById(\'hidden_form\').op.value=\'redo_application\';document.getElementById(\'hidden_form\').submit()"');
        $form->addSubmit('submit_application', 'Submit application');

        $form->mergeTemplate($tpl);
        $tpl = $form->getTemplate();
        
        return PHPWS_Template::process($tpl, 'hms', 'student/student_application.tpl');
    }

    function submit_application_review()
    {
        PHPWS_Core::initModClass('hms', 'HMS_Application.php');
        PHPWS_Core::initModClass('hms', 'HMS_SOAP.php');

        # Create a new application from the request data and save it
        $application = &new HMS_Application($_SESSION['asu_username'], $_SESSION['application_term']);
        
        $application->term                  = $_SESSION['application_term'];
        $application->meal_option           = $_REQUEST['meal_option'];
        $application->lifestyle_option      = $_REQUEST['lifestyle_option'];
        $application->preferred_bedtime     = $_REQUEST['preferred_bedtime'];
        $application->room_condition        = $_REQUEST['room_condition'];
        $application->rlc_interest          = $_REQUEST['rlc_interest'];
        $application->agreed_to_terms       = $_REQUEST['agreed_to_terms'];

        if(isset($_REQUEST['special_needs']['physical_disability'])){
            $application->physical_disability = 1;
        }

        if(isset($_REQUEST['special_needs']['psych_disability'])){
            $application->psych_disability = 1;
        }

        if(isset($_REQUEST['special_needs']['medical_need'])){
            $application->medical_need = 1;
        }

        if(isset($_REQUEST['special_needs']['gender_need'])){
            $application->gender_need = 1;
        }

        $application->gender                = HMS_SOAP::get_gender($application->asu_username, TRUE);
        
        $type   = HMS_SOAP::get_student_type($application->asu_username, $application->term);
        $class  = HMS_SOAP::get_student_class($application->asu_username, $application->term);

        #TODO: Get rid of these aweful magic numbers
        switch($type){
            case TYPE_FRESHMEN:
                $application->student_status = 1;
                break;
            case TYPE_TRANSFER:
                $application->student_status = 2;
                break;
        }

        switch($class){
            case CLASS_FRESHMEN:
                $application->term_classification = 1;
                break;
            case CLASS_SOPHOMORE:
                $application->term_classification = 2;
                break;
            case CLASS_JUNIOR:
                $application->term_classification = 3;
                break;
            case CLASS_SENIOR:
                $application->term_classification = 4;
                break;
        }

        $application->aggregate = $application->calculateAggregate();
        
        $result = $application->save();

        $tpl = array();
        
        if($result == TRUE){
            # report the application to banner;
            $application->report_to_banner();
        }else{
            # Show an error
            $tpl['TITLE'] = 'Error';
            $tpl['MESSAGE'] = 'There was an error saving your application. Please try again.';
            return PHPWS_Template::process($tpl,'hms', 'student/student_success_failure_message.tpl');
        }

        if($_REQUEST['rlc_interest'] == 1){
            # Show the RLC application
            PHPWS_Core::initModClass('hms', 'HMS_RLC_Application.php');
            return HMS_RLC_Application::show_rlc_application_form();
        }else{
            # Show the regular menu
            $tpl['VIEW_APPLICATION']    = PHPWS_Text::secureLink(_('View My Application'), 'hms', array('type'=>'student', 'op'=>'view_application'));
            $tpl['MAIN_MENU_LINK']      = PHPWS_Text::secureLink(_('Back to Main Menu'), 'hms', array('type'=>'student','op'=>'main'));
            $tpl['LOGOUT_LINK']         = PHPWS_Text::moduleLink(_('Logout'), 'users', array('action'=>'user', 'command'=>'logout'));
            
            PHPWS_Core::initModClass('hms','HMS_Entry_Term.php');
            if(HMS_Entry_Term::get_entry_semester($_SESSION['asu_username']) == TERM_FALL){
                $tpl['RLC_LINK'] = PHPWS_Text::secureLink(_('Unique Housing Options Application'), 'hms', array('type'=>'student', 'op'=>'show_rlc_application_form'));
            }
            
            return PHPWS_Template::process($tpl, 'hms', 'student/student_application_thankyou.tpl');
        }

    }

    function view_housing_application($username,$term)
    {
        $application = &new HMS_Application($username, $term);

        if(!$application || is_null($application)){
            return "No applicatin found for the specified user and term.";
        }
        
        $tpl = array();

        $tpl['STUDENT_NAME']    = HMS_SOAP::get_full_name($username);
        $tpl['GENDER']          = (HMS_SOAP::get_gender($username,TRUE) == FEMALE) ? FEMALE_DESC : MALE_DESC;
        $tpl['ENTRY_TERM']      = HMS_Term::term_to_text($term, TRUE);
        $tpl['CLASSIFICATION_FOR_TERM_LBL'] = HMS_Util::formatClass(HMS_SOAP::get_student_class($username, $term));
        $tpl['STUDENT_STATUS_LBL']          = HMS_Util::formatType(HMS_SOAP::get_student_type($username, $term));

        $tpl['MEAL_OPTION']         = HMS_Util::formatMealOption($application->meal_option);
        $tpl['LIFESTYLE_OPTION']    = $application->lifestyle_option == 1?'Single gender':'Co-ed';
        $tpl['PREFERRED_BEDTIME']   = $application->preferred_bedtime == 1?'Early':'Late';
        $tpl['ROOM_CONDITION']      = $application->room_condition == 1?'Clean':'Dirty';
        
        $special_needs = "";
        if($application->physical_disability == 1){
            $special_needs = 'Physical disability<br />';
        }
        if($application->psych_disability){
            $special_needs .= 'Psychological disability<br />';
        }
        if($application->medical_need){
            $special_needs .= 'Medical need<br />';
        }
        if($application->gender_need){
            $special_needs .= 'Gender need<br />';
        }

        if($special_needs == ''){
            $special_needs = 'None';
        }
        $tpl['SPECIAL_NEEDS_RESULT'] = $special_needs;

        $tpl['RLC_INTEREST_1'] = $application->rlc_interest == 0?'No':'Yes';

        if(Current_User::getUsername() == "hms_student"){
            $tpl['MENU_LINK'] = PHPWS_Text::secureLink('Back to main menu', 'hms', array('type'=>'student', 'op'=>'show_main_menu'));
        }

        return PHPWS_Template::process($tpl, 'hms', 'student/student_application.tpl');
    }

}
?>
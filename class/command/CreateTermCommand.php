<?php

class CreateTermCommand extends Command {

    public function getRequestVars()
    {
        return array('action'=>'CreateTerm');
    }

    public function execute(CommandContext $context)
    {

        if(!UserStatus::isAdmin() || !Current_User::allow('hms', 'edit_terms')) {
            PHPWS_Core::initModClass('hms', 'exception/PermissionException.php');
            throw new PermissionException('You do not have permission to edit terms.');
        }

        $successCmd = CommandFactory::getCommand('ShowEditTerm');
        $errorCmd = CommandFactory::getCommand('ShowCreateTerm');

        $year = $context->get('year_drop');
        $sem = $context->get('term_drop');

        if(!isset($year) || is_null($year) || empty($year)){
            NQ::simple('hms', HMS_NOTIFICATION_ERROR, 'You must provide a year.');
            $viewCmd->redirect();
        }

        if(!isset($sem) || is_null($sem) || empty($sem)){
            NQ::simple('hms', HMS_NOTIFICATION_ERROR, 'You must provide a semester.');
            $viewCmd->redirect();
        }

        // Check to see if the specified term already exists
        if(Term::isValidTerm($year . $sem)){
            NQ::simple('hms', HMS_NOTIFICATION_ERROR, 'Error: That term already exists.');
            $errorCmd->redirect();
        }

        $term = new Term(NULL);
        $term->setTerm($year . $sem);
        $term->setBannerQueue(1);

        try{
            $term->save();
        }catch(DatabaseException $e){
            NQ::simple('hms', HMS_NOTIFICATION_ERROR, 'There was an error saving the term. Please try again or contact ESS.');
            $viewCmd->redirect();
        }

        $text = Term::toString($term->getTerm());

        $copy = $context->get('copy_drop');
        
        if($copy == 'struct'){
            // Only hall structure
            $copyAssignments = false;
        }else if($copy == 'struct_assign'){
            // Hall structure and assignments
            $copyAssignments = true;
        }else{
            // either $copy == 'nothing', or the view didn't specify... either way, we're done
            NQ::simple('hms', HMS_NOTIFICATION_SUCCESS, "$text term created successfully.");
            $successCmd->redirect();
        }

        PHPWS_Core::initModClass('hms', 'HMS_Residence_Hall.php');
        PHPWS_Core::initModClass('hms', 'HousingApplication.php');

        $db = new PHPWS_DB();

        try{
            $db->query('BEGIN');
            # Get the halls from the current term
            $halls = HMS_Residence_Hall::get_halls(Term::getCurrentTerm());
            set_time_limit(36000);

            foreach ($halls as $hall){

                // we always copy hall structure!
                $hall->copy($term->getTerm());

                if($copyAssignments){
                    $assignees = $hall->get_assignees();
                    foreach($assignees as $student){
                        // Get student's old assignment and application
                        $assignment = HMS_Assignment::getAssignment($student->getUsername(), Term::getCurrentTerm());
                        $app = HousingApplication::getApplicationByUser($student->getUsername(), Term::getCurrentTerm());
                        
                        // Meal option is set to standard by default
                        $meal_option = BANNER_MEAL_STD;
                        if(!is_null($app)){
                            $meal_option = $app->getMealPlan();
                        }

                        $room_id = $assignment->get_room_id();
                        $bed_id = $assignment->bed_id;

                        $note = ", Assignment copied from ".Term::getCurrentTerm()." to ".$term->getTerm();
                        $result = HMS_Assignment::assignStudent($student, $term->getTerm(), $room_id, $bed_id, $meal_option, $note);
                    }
                }

            }

            $db->query('COMMIT');

        }catch(Exception $e){

            $db->query('ROLLBACK');
            NQ::simple('hms', HMS_NOTIFICATION_ERROR, 'There was an error copying the hall structure and/or assignments. The term was created, but nothing was copied.');
            $errorCmd->redirect();
        }

        if($copyAssignments){
            NQ::simple('hms', HMS_NOTIFICATION_SUCCESS, "$text term created successfully. The hall structure and assignments were copied successfully.");
        }else{
            NQ::simple('hms', HMS_NOTIFICATION_SUCCESS, "$text term created successfully and hall structure copied successfully.");
        }
        Term::setSelectedTerm($term->getTerm());
        $successCmd->redirect();
    }
}

?>
<?php
/**
 * SendRoomDamageNotificationsCommand.php
 *
 * Sends email notifications to students with assessed room damages
 * reported in the selected term.
 *
 * @author jbooker
 * @package homestead
 */

class SendRoomDamageNotificationsCommand extends Command {

    public function getRequestVars()
    {
    	return array('action'=>'SendRoomDamageNotifications');
    }

    public function execute(CommandContext $context)
    {
        PHPWS_Core::initModClass('hms', 'RoomDamageFactory.php');
        PHPWS_Core::initModClass('hms', 'StudentFactory.php');
        PHPWS_Core::initModClass('hms', 'HMS_Email.php');
        PHPWS_Core::initModClass('hms', 'CheckinFactory.php');
        PHPWS_Core::initModClass('hms', 'HMS_Bed.php');

        $term = Term::getSelectedTerm();

        // Get the total damages assessed for each student
        $damageAssessments = RoomDamageFactory::getAssessedDamagesStudentTotals($term);

        $missingCoord = array();

        foreach($damageAssessments as $dmg)
        {
        	$student = StudentFactory::getStudentByBannerId($dmg['banner_id'], $term);

            // Get the student's last checkout
            // (NB: the damages may be for multiple check-outs,
            // but we'll just take the last one)
            $checkout = CheckinFactory::getLastCheckoutForStudent($student);

            $bed = new HMS_Bed($checkout->getBedId());
            $room = $bed->get_parent();
            $floor = $room->get_parent();
            $hall = $floor->get_parent();

            $coordinators = $hall->getCoordinators();



            if($coordinators != null){
            	$coordinatorName  = $coordinators[0]->getDisplayName();
                $coordinatorEmail = $coordinators[0]->getEmail();
                HMS_Email::sendDamageNotification($student, $term, $dmg['sum'], $coordinatorName, $coordinatorEmail);
                RoomDamageFactory::setAssessedDmgsToEmailed($dmg['id']);
            }
            else
            {
            	if(!in_array($hall->getHallName(), $missingCoord))
                {
                    $missingCoord[] = $hall->getHallName();
                }
            }
        }

        if(!empty($missingCoord))
        {
            //Show an error message
            $message = 'The area coordinator is missing from these halls: ';

            $i = 0;
            $message .= $missingCoord[$i++];

            for($i; $i < count($missingCoord); $i++)
            {
                $message .= ', ' . $missingCoord[$i];
            }

            $message .= '. Students from these halls were not sent emails.';

            NQ::simple('hms', hms\NotificationView::ERROR, $message);
        }
        else {
            // Show a success message
        	NQ::simple('hms', hms\NotificationView::SUCCESS, 'Room damage notices sent.');
        }

        // Redirect back to main menu
        $cmd = CommandFactory::getCommand('ShowAdminMaintenanceMenu');
        $cmd->redirect();
    }
}

<?php
PHPWS_Core::initModClass('hms', 'StudentFactory.php');
PHPWS_Core::initModClass('hms', 'HousingApplicationFactory.php');
PHPWS_Core::initModClass('hms', 'CheckinFactory.php');
PHPWS_Core::initModClass('hms', 'HMS_Assignment.php');
PHPWS_Core::initModClass('hms', 'HMS_Activity_Log.php');


class CheckoutFormSubmitCommand extends Command {

    private $bannerId;

    private $hallId;

    private $assignmentId;

    public function setBannerId($bannerId)
    {
        $this->bannerId = $bannerId;
    }

    public function setHallId($hallId)
    {
        $this->hallId = $hallId;
    }

    public function setAssignmentId($assignId)
    {
        $this->assignmentId = $assignId;
    }

    public function getRequestVars()
    {
        return array (
                'action' => 'CheckoutFormSubmit',
                'bannerId' => $this->bannerId,
                'hallId' => $this->hallId,
                'assignmentId' => $this->assignmentId
        );
    }

    public function execute(CommandContext $context)
    {
        // Check permissions
        if (!Current_User::allow('hms', 'checkin')) {
            PHPWS_Core::initModClass('hms', 'exception/PermissionException.php');
            throw new PermissionException('You do not have permission to checkin students.');
        }

        $bannerId = $context->get('bannerId');
        $hallId = $context->get('hallId');

        // Check for key code
        $keyCode = $context->get('key_code');
        $keyNotReturned = $context->get('key_not_returned');

        if (!isset($keyNotReturned) && (!isset($keyCode) || $keyCode == '')) {
            NQ::simple('hms', HMS_NOTIFICATION_ERROR, 'Please enter a key code.');
            $errorCmd = CommandFactory::getCommand('ShowCheckoutForm');
            $errorCmd->setBannerId($bannerId);
            $errorCmd->setHallId($hallId);
            $errorCmd->redirect();
        }

        $improperCheckout = $context->get('improper_checkout');

        $term = Term::getCurrentTerm();

        // Lookup the student
        $student = StudentFactory::getStudentByBannerId($bannerId, $term);

        // Create the actual check-in and save it
        $assignment = HMS_Assignment::getAssignmentByBannerId($bannerId, $term);
        $bed = $assignment->get_parent();

        $currUser = Current_User::getUsername();

        // Get the existing check-in
        $checkin = CheckinFactory::getCheckinByBed($student, $bed, $term);

        // Make sure we found a check-in
        if (is_null($checkin)) {
            NQ::simple('hms', HMS_NOTIFICATION_ERROR, "Sorry, we couldn't find a corresponding check-in for this check-out.");
            $errorCmd = CommandFactory::getCommand('ShowCheckoutForm');
            $errorCmd->setBannerId($bannerId);
            $errorCmd->setHallId($hallId);
            $errorCmd->redirect();
        }

        // Make sure the checkin we're working with was for the same hall/room we're checking out of
        // TODO

        // Set checkout date and user
        $checkin->setCheckoutDate(time());
        $checkin->setCheckoutBy($currUser);

        // Improper checkout handling
        if (isset($improperCheckout)) {
            $checkin->setImproperCheckout(true);
        } else {
            $checkin->setImproperCheckout(false);
        }

        if (isset($keyNotReturned)) {
            $checkin->setKeyNotReturned(true);
        } else {
            $checkin->setKeyNotReturned(false);
        }

        // Save the check-in
        $checkin->save();

        // Add this to the activity log
        HMS_Activity_Log::log_activity($student->getUsername(), ACTIVITY_CHECK_OUT, UserStatus::getUsername(), $assignment->where_am_i());

        // Generate the RIC
        PHPWS_Core::initModClass('hms', 'InfoCard.php');
        PHPWS_Core::initModClass('hms', 'InfoCardPdfView.php');
        $infoCard = new InfoCard($checkin);

        $view = new InfoCardPdfView();
        $view->addInfoCard($infoCard);

        // Email the RIC form to the student
        //TODO: Refactor this into an actual messageing system where messages extend from a common parent class
        require_once PHPWS_SOURCE_DIR . 'mod/hms/lib/SwiftMailer/swift_required.php';

        $message = Swift_Message::newInstance();

        $message->setSubject('Check-out Confirmation');
        $message->setFrom(array('uha@appstate.edu' => 'University Housing'));
        $message->setTo(array(($student->getUsername() . '@appstate.edu') => $student->getName()));
        $message->setBody('ohhh hai');
        $message->addPart('<em>Here is the html part</em>', 'text/html');

        // Attach info card
        $attachment = Swift_Attachment::newInstance($view->getPdf()->output('my-pdf-file.pdf', 'S'), 'my-file.pdf', 'application/pdf');
        $message->attach($attachment);

        $transport = Swift_SmtpTransport::newInstance('localhost');
        $mailer = Swift_Mailer::newInstance($transport);

        $mailer->send($message);


        NQ::simple('hms', HMS_NOTIFICATION_SUCCESS, 'Checkout successful.');

        // Redirect to start of checkout process
        $cmd = CommandFactory::getCommand('ShowCheckoutStart');

        $cmd->redirect();
    }
}
?>

<?php

/**
 *
 * @author Matthew McNaney <mcnaney at gmail dot com>
 */

class AppliedStudentData extends Report implements iCsvReport {
    const friendlyName = 'Applied Student Data Export';
    const shortName = 'AppliedStudentData';

    private $term;
    private $rows;

    public function setTerm($term)
    {
        $this->term = $term;
    }

    public function getTerm()
    {
        return $this->term;
    }

    public function execute()
    {
        PHPWS_Core::initModClass('hms', 'HousingApplicationFactory.php');
        PHPWS_Core::initModClass('hms', 'HMS_Assignment.php');
        PHPWS_Core::initModClass('hms', 'StudentFactory.php');

        $db = new PHPWS_DB('hms_new_application');
        $db->addColumn('hms_new_application.*');
        $db->addWhere('term', $this->term);
        $db->addWhere('cancelled', 0);
        $term = Term::getTermSem($this->term);

        if($term == TERM_FALL)
        {
            $db->addJoin('LEFT', 'hms_new_application', 'hms_fall_application', 'id', 'id');
            $db->addColumn('hms_fall_application.*');
        }

        $result = $db->select();

        $app = array();

        foreach ($result as $app) {

            $username   = $app['username'];
            $bannerId   = $app['banner_id'];
            $type       = $app['student_type'];
            $cellPhone  = $app['cell_phone'];
            $date       = date('n/j/Y', $app['created_on']);


            $assignment = HMS_Assignment::getAssignmentByBannerId($bannerId, $this->term);

            if(!is_null($assignment)){
                $room = $assignment->where_am_i();
            }else{
                $room = '';
            }

            $student = StudentFactory::getStudentByBannerId($bannerId, $this->term);

            $first  = $student->getFirstName();
            $middle = $student->getMiddleName();
            $last   = $student->getLastName();
            $gender = $student->getPrintableGender();
            $birthday = date("m/d/Y", $student->getDobDateTime()->getTimestamp());

            $address = $student->getAddress(NULL);

            $lifestyle = ($app['lifestyle_option'] == 1) ? 'Single Gender' : 'Co-Ed';

            if(!is_null($address) && $address !== false){
                $this->rows[] =
                array(
                        $username, $bannerId, $first, $middle, $last, $gender,
                        $type, $cellPhone, $room, $date, $address->line1, $address->line2,
                        $address->line3, $address->city,
                        $address->state, $address->zip, $birthday, $lifestyle
                     );
            }else{
                $this->rows[] =
                array($username, $bannerId, $first, $middle, $last, '',
                      $type, $cellPhone, $room, $date, '', '', '', '', '', '', $lifestyle);
            }
        }
    }

    public function getCsvColumnsArray()
    {
        return array('Username', 'Banner id', 'First name', 'Middle name',
            'Last name', 'Gender', 'Student type', 'Cell Phone', 'Assignment', 'Date Applied', 'Address 1',
            'Address 2', 'Address 3', 'City', 'State', 'Zip', 'Birthday', 'Lifestyle');
    }

    public function getCsvRowsArray()
    {
        return $this->rows;
    }

    public function getDefaultOutputViewCmd()
    {
        $cmd = CommandFactory::getCommand('ShowReportCsv');
        $cmd->setReportId($this->id);

        return $cmd;
    }
}

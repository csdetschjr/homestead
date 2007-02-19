<?php

/**
 * Room objects for HMS
 *
 * @author Kevin Wilcox <kevin at tux dot appstate dot edu>
 */

class HMS_Room
{
    var $id;
    var $room_number;
    var $building_id;
    var $floor_number;
    var $floor_id;
    var $is_online;
    var $gender_type;
    var $capacity_per_room;
    var $phone_number;
    var $is_medical;
    var $is_reserved;
    var $added_by;
    var $added_on;
    var $updated_by;
    var $updated_on;
    var $error;

    function HMS_Room()
    {
        $this->id = NULL;
        $this->is_online = NULL;
        $this->error = "";
    }
    
    function set_error_msg($msg)
    {
        $this->error .= $msg;
    }

    function get_error_msg()
    {
        return $this->error;
    }

    function set_id($id)
    {
        $this->id = $id;
    }

    function get_id()
    {
        return $this->id;
    }

    function set_room_number($number)
    {
        $this->room_number = $number;
    }

    function get_room_number($id = NULL)
    {
        if($id != NULL) {
            $db = &new PHPWS_DB('hms_room');
            $db->addColumn('room_number');
            $db->addWhere('id', $id);
            $room_number = $db->select('one');
            return $room_number;
        } else {
            return $this->room_number;
        }
    }

    function set_building_id($building)
    {
        $this->building_id = $building;
    }

    function get_building_id()
    {
        return $this->building_id;
    }

    function set_floor_number($floor)
    {
        $this->floor_number = $floor;
    }

    function get_floor_number($id = NULL)
    {
        if($id == NULL) {
            return $this->floor_number;
        } else {
            $db = &new PHPWS_DB('hms_room');
            $db->addColumn('floor_number');
            $db->addWhere('id', $id);
            $db->addWhere('deleted', 0);
            $floor_number = $db->select('one');
            return $floor_number;
        }
    }

    function set_floor_id($floor)
    {
        $this->floor_id = $floor;
    }

    function get_floor_id($bid = NULL, $floor = NULL)
    {
        if($bid != NULL && $floor != NULL) {
            $floor_db = &new PHPWS_DB('hms_floor');
            $floor_db->addWhere('building', $bid);
            $floor_db->addWhere('floor_number', $floor);
            $floor_db->addWhere('deleted', '0');
            $floor_db->addColumn('id');
            $fid = $floor_db->select('one');
            return $fid;
        } else {
            return $this->floor_id;
        }
    }

    function set_gender_type($gender, $id = NULL, $building = NULL)
    {
        if($building != NULL) {
            $db = &new PHPWS_DB('hms_room');
            $db->addWhere('building_id', $building);
            $db->addWhere('deleted', '0');
            $db->addValue('gender_type', $gender);
            $success = $db->update();
            if(PEAR::isError($success)) {
                PHPWS_Error::log($success, 'hms', 'HMS_Room::set_gender_type');
            }
            return $success;
        } else {
            $this->gender_type = $gender;
        }
    }

    function get_gender_type($id = NULL)
    {
        if($id != NULL) {
            $db = new PHPWS_DB('hms_room');
            $db->addColumn('gender_type');
            $db->addWhere('id', $id);
            $result = $db->select('one');
            return $result;
        } else {
            return $this->gender_type;
        }
    }

    function set_is_online($online, $id = NULL, $building = NULL)
    {
        if($building != NULL) {
            $db = &new PHPWS_DB('hms_room');
            $db->addWhere('building_id', $building);
            $db->addWhere('deleted', '0');
            $db->addValue('is_online', $online);
            $success = $db->update();
            if(PEAR::isError($success)) {
                PHPWS_Error::log($success, 'hms', 'HMS_Floor::set_is_online');
            }
            return $success;
        } else {
            $this->is_online = $online;
        }
    }

    function get_is_online()
    {
        return $this->is_online;
    }

    function set_capacity_per_room($capacity)
    {
        $this->capacity_per_room = $capacity;
    }

    function get_capacity_per_room($id = NULL)
    {
        if($id != NULL) {
            $db = new PHPWS_DB('hms_room');
            $db->addColumn('capacity_per_room');
            $db->addWhere('id', $id);
            $capacity = $db->select('one');
            return $capacity;
        } else {
            return $this->capacity_per_room;
        }
    }

    function set_phone_number($phone)
    {
        $this->phone_number = $phone;
    }

    function get_phone_number()
    {
        return $this->phone_number;
    }

    function set_is_medical($medical)
    {
        $this->is_medical = $medical;
    }

    function get_is_medical($id = NULL)
    {
        if($id != NULL) {
            $db = new PHPWS_DB('hms_room');
            $db->addColumn('is_medical');
            $db->addWhere('id', $id);
            $medical = $db->select('one');
            return $medical;
        } else {
            return $this->is_medical;
        }
    }

    function set_is_reserved($reserved)
    {
        $this->is_reserved = $reserved;
    }

    function get_is_reserved($id = NULL)
    {
        if($id != NULL) {
            $db = new PHPWS_DB('hms_room');
            $db->addColumn('is_reserved');
            $db->addWhere('id', $id);
            $reserved = $db->select('one');
            return $reserved;
        } else {
            return $this->is_reserved;
        }
    }

    function set_added_by_on()
    {
        $this->set_added_by();
        $this->set_added_on();
    }

    function set_added_by()
    {
        $this->added_by = Current_User::getId();
    }

    function set_added_on()
    {
        $this->added_on = time();
    }

    function set_updated_by_on()
    {
        $this->set_updated_by();
        $this->set_updated_on();
    }

    function set_updated_by()
    {
        $this->updated_by = Current_User::getId();
    }

    function set_updated_on()
    {
        $this->updated_on = time();
    }

    function set_variables()
    {
        if($_REQUEST['id']) $this->set_id($_REQUEST['id']);
        $this->set_room_number($_REQUEST['room_number']);
        $this->set_building_id($_REQUEST['building_id']);
        $this->set_floor_number($_REQUEST['floor_number']);
        $this->set_floor_id($_REQUEST['floor_id']);
        $this->set_gender_type($_REQUEST['gender_type']);
        $this->set_capacity_per_room($_REQUEST['capacity_per_room']);
        $this->set_phone_number($_REQUEST['phone_number']);
        $this->set_is_medical($_REQUEST['is_medical']);
        $this->set_is_reserved($_REQUEST['is_reserved']);
        $this->set_is_online($_REQUEST['is_online']);
    }

    function save_room()
    {
        $db = &new PHPWS_DB('hms_room');
        $db->addWhere('id', $_REQUEST['id']);
        $db->addValue('gender_type', $_REQUEST['gender_type']);
        $db->addValue('capacity_per_room', $_REQUEST['capacity_per_room']);
        if($_REQUEST['phone_number'] != NULL) {
            $db->addValue('phone_number', $_REQUEST['phone_number']);
        }
        $db->addValue('is_medical', $_REQUEST['is_medical']);
        $db->addValue('is_reserved', $_REQUEST['is_reserved']);
        $db->addValue('is_online', $_REQUEST['is_online']);
        $db->addValue('updated_by', Current_User::getId());
        $db->addValue('updated_on', time());

        $success = $db->update();
        if(PEAR::isError($success)) {
            PHPWS_Error::log($success, 'hms', 'HMS_Room::save_room');
            $final = "Error saving Room. Please consult Electronic Student Services.<br />";
            $final .= "Error: $success";
        } else {
            $final = "Room successfully saved!";
        }
        return $final;
    }
    
    function save_room_object($object)
    {
        $db = &new PHPWS_DB('hms_room');
        if(!$object->get_id()) {
            $object->set_added_by_on();
            $object->set_updated_by_on();
        }
        $success = $db->saveObject($object);
        if(PEAR::isError($success)) {
            test($success);
            PHPWS_Error::log($success);
            return $success;
        }
    }

    function delete_rooms_by_floor($bid, $floor = NULL, $one = FALSE)
    {
        $room_db = &new PHPWS_DB('hms_room');
        $room_db->addWhere('building_id', $bid);
        if($floor != NULL && $one == FALSE) {
            $room_db->addWhere('floor_number', $floor, '>');
        } else if ($floor != NULL && $one == TRUE) {
            $room_db->addWhere('floor_number', $floor);
        }
        $room_db->addValue('deleted', 1);
        $room_db->addValue('deleted_by', Current_User::getId());
        $room_db->addValue('deleted_on', time());
        $result = $room_db->update();
        
        if(PEAR::isError($result)) {
            PHPWS_Error::log($result, 'hms', 'HMS_Room::delete_rooms_by_floor');
            return $result;
        }

        return $result;
    }

    function delete_room($bid, $room_number)
    {
        $room_db = &new PHPWS_DB('hms_room');
        $room_db->addValue('deleted', '1');
        $room_db->addWhere('building_id', $bid);
        $room_db->addWhere('room_number', $room_number);
        $room_db->addValue('deleted_by', Current_User::getId());
        $room_db->addValue('deleted_on', time());
        $success = $room_db->update();
        return $success;
    }

    function change_rooms_per_floor($bid, $number_floors, $old_rooms_per_floor, $new_rooms_per_floor, $is_online = NULL, $gender_type = NULL, $capacity_per_room = NULL)
    {
        if($old_rooms_per_floor > $new_rooms_per_floor) {
            // rooms per floor has decreased
            for($floor = 1; $floor <= $number_floors; $floor++) {
                for($room = $new_rooms_per_floor + 1; $room <= $old_rooms_per_floor; $room++) {
                    $room_number = $floor . str_pad($room, 2, '0', STR_PAD_LEFT);
                    $success = HMS_Room::delete_room($bid, $room_number);
                } // end for ($room)
            } // end for ($floor)
        } else if($old_rooms_per_floor < $new_rooms_per_floor) {
            // rooms per floor has increased
            for($floor = 1; $floor <= $number_floors; $floor++) {
                $fid = HMS_Room::get_floor_id($bid, $floor);
                for($room = $old_rooms_per_floor + 1; $room <= $new_rooms_per_floor; $room++) {
                    $room_obj = &new HMS_Room;
                    $room_obj->set_room_number($floor . str_pad($room, 2, '0', STR_PAD_LEFT));
                    $room_obj->set_building_id($bid);
                    $room_obj->set_floor_number($floor);
                    $room_obj->set_gender_type($gender_type);
                    $room_obj->set_is_online($is_online);
                    $room_obj->set_is_reserved('0');
                    $room_obj->set_is_medical('0');
                    $room_obj->set_floor_id($fid);
                    $room_obj->set_capacity_per_room($capacity_per_room);
                    $room_obj->set_added_by_on();
                    $room_obj->set_updated_by_on();
                    $db = &new PHPWS_DB('hms_room');
                    $success = $db->saveObject($room_obj);
                }
            } // end for
        } // end else if
    } // end function

    function change_capacity_per_room($capacity, $bid)
    {
        $db = &new PHPWS_DB('hms_room');
        $db->addWhere('building_id', $bid);
        $db->addValue('capacity_per_room', $capacity);
        $db->addWhere('deleted', 0);
        $db->addValue('updated_by', Current_User::getId());
        $db->addValue('updated_on', time());
        $result = $db->update();
        if(PEAR::isError($result)) {
            PHPWS_Core::log($result, 'hms', 'HMS_Room::change_capacity_per_room');
        }
        return $result;
    }

    function select_residence_hall_for_edit_room()
    {
        PHPWS_Core::initModClass('hms', 'HMS_Forms.php');
        $form = &new HMS_Form;
        return $form->select_residence_hall_for_edit_room();
    }

    function select_floor_for_edit_room()
    {
        PHPWS_Core::initModClass('hms', 'HMS_Forms.php');
        $form = &new HMS_Form;
        return $form->select_floor_for_edit_room();
    }

    function select_room_for_edit()
    {
        PHPWS_Core::initModClass('hms', 'HMS_Forms.php');
        $form = &new HMS_Form;
        return $form->select_room_for_edit();
    }

    function edit_room()
    {
        PHPWS_Core::initModClass('hms', 'HMS_Forms.php');
        $form = &new HMS_Form;
        return $form->edit_room();
    }

    function is_in_suite($id = NULL)
    {
        if($id == 0) return false;

        $db = &new PHPWS_DB('hms_suite');
       
        $db->addColumn('id');
        $db->addColumn('room_id_zero');
        $db->addColumn('room_id_one');
        $db->addColumn('room_id_two');
        $db->addColumn('room_id_three');

        if($id == NULL) {
            $db->addWhere('room_id_zero', $this->get_id());
            $db->addWhere('room_id_one', $this->get_id(), '=', 'OR');
            $db->addWhere('room_id_two', $this->get_id(), '=', 'OR');
            $db->addWhere('room_id_three', $this->get_id(), '=', 'OR');
        } else {
            $db->addWhere('room_id_zero', $id);
            $db->addWhere('room_id_one', $id, '=', 'OR');
            $db->addWhere('room_id_two', $id, '=', 'OR');
            $db->addWhere('room_id_three', $id, '=', 'OR');
        }

        $row = $db->select('row');
        
        if(PEAR::isError($row)) {
            PHPWS_Error::log($row, 'hms', 'HMS_Room::is_in_suite');
            return FALSE;
        }

        if($row == NULL or $row == FALSE) {
            return FALSE;
        } else {
            return $row;
        }
    }

    function get_suite_number($id)
    {
        $db = &new PHPWS_DB('hms_suite');
        $db->addColumn('id');
        $db->addWhere('room_id_zero', $id);
        $db->addWhere('room_id_one', $id, '=', 'OR');
        $db->addWhere('room_id_two', $id, '=', 'OR');
        $db->addWhere('room_id_three', $id, '=', 'OR');
        $suite = $db->select('one');
    
        if(PEAR::isError($suite)) {
            PHPWS_Error::log($suite, 'hms', 'HMS_Room::get_suite_number');
            return ROOM_NOT_IN_SUITE;
        } else {
            return $suite;
        }
    }

    function get_rooms_on_floor($id)
    {
        $floor_id_db = new PHPWS_DB('hms_room');
        $floor_id_db->addColumn('floor_id');
        $floor_id_db->addWhere('id', $id);
        $floor_id = $floor_id_db->select('one');

        $rooms_db = new PHPWS_DB('hms_room');
        $rooms_db->addColumn('id');
        $rooms_db->addColumn('room_number');
        $rooms_db->addWhere('floor_id', $floor_id);
        $rooms_db->addWhere('deleted', '0');
        $rooms_db->addOrder('room_number');
        $rooms_raw = $rooms_db->select();
        
        foreach($rooms_raw as $room) {
            $room_list[$room['id']] = $room['room_number'];
        }

        return $room_list;
    }

    function get_hall_name_from_floor_id($id)
    {
        $db = new PHPWS_DB('hms_room');
        $db->addColumn('building_id');
        $db->addWhere('id', $id);
        $db->addWhere('deleted', 0);
        $bid = $db->select('one');

        $db = new PHPWS_DB('hms_residence_hall');
        $db->addColumn('hall_name');
        $db->addWhere('id', $bid);
        $db->addWhere('deleted', 0);
        $name = $db->select('one');

        return $name;
    }

    function is_valid_room($id)
    {
        if($id == '0') return true;

        $db = &new PHPWS_DB('hms_room');
        $db->addColumn('id');
        $db->addWhere('id', $id);
        $exists = $db->select('one');

        if(PEAR::isError($exists)) {
            PHPWS_Error::log($exists, 'hms', 'HMS_Room::is_valid_room');
            return false;
        } else if ($exists == NULL) {
            return false;
        } else {
            return true;
        }
    }

    function main()
    {
        switch($_REQUEST['op'])
        {
            case 'select_hall_for_edit_room':
                return HMS_Room::select_residence_hall_for_edit_room();
                break;
            case 'select_floor_for_edit_room':
                return HMS_Room::select_floor_for_edit_room();
                break;
            case 'select_room_for_edit':
                return HMS_Room::select_room_for_edit();
                break;
            case 'edit_room':
                return HMS_Room::edit_room();
                break;
            case 'save_room':
                return HMS_Room::save_room();
                break;
            default:
                return $_REQUEST['op'] . " is the operation<br />";
                break;
        }
    }
};
?>

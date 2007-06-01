ALTER TABLE hms_learning_community_assignment ADD COLUMN temp_name character varying(32);
UPDATE hms_learning_community_assignment SET temp_name = asu_username;
ALTER TABLE hms_learning_community_assignment DROP COLUMN asu_username;
ALTER TABLE hms_learning_community_assignment ADD COLUMN asu_username character varying(32);
UPDATE hms_learning_community_assignment SET asu_username = temp_name;
ALTER TABLE hms_learning_community_assignment DROP COLUMN temp_name;

ALTER TABLE hms_room ADD COLUMN displayed_room_number character varying(8);
UPDATE hms_room SET displayed_room_number = room_number;

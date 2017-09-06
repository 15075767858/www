CREATE TABLE `smartio_device` (
`id` int UNSIGNED NOT NULL AUTO_INCREMENT,
`device` varchar(4) NOT NULL,
`ip` varchar(20) NOT NULL,
`port` int UNSIGNED NULL,
PRIMARY KEY (`id`) 
);

CREATE TABLE `smartio_key` (
`id` int UNSIGNED NOT NULL AUTO_INCREMENT,
`key` varchar(7) NOT NULL,
`device` int UNSIGNED NOT NULL,
`Object_Name` varchar(255) NULL,
`Present_Value` varchar(255) NULL,
`Notify_Type` varchar(255) NULL,
`Description` varchar(255) NULL,
`Update_Time` timestamp NULL,
`Object_Type` varchar(64) NULL,
`Hide` varchar(64) NULL,
`Update_Interval` varchar(64) NULL,
`Max_Pres_Value` varchar(64) NULL,
`High_Limit` varchar(64) NULL,
`Acked_Transitions` varchar(64) NULL,
`Min_Pres_Value` varchar(64) NULL,
`Out_Of_Service` varchar(64) NULL,
`Limit_Enable` varchar(64) NULL,
`Event_Enable` varchar(64) NULL,
`Event_State` varchar(64) NULL,
`Deadband` varchar(64) NULL,
`Low_Limit` varchar(64) NULL,
`Device_Type` varchar(64) NULL,
`Object_Identifier` varchar(64) NULL,
`Notification_Class` varchar(64) NULL,
`Lock_Enable` varchar(64) NULL,
`UnitsObject_Type` varchar(64) NULL,
`Offset` varchar(64) NULL,
`COV_Increment` varchar(64) NULL,
`Status_Flags` varchar(64) NULL,
`Time_Delay` varchar(64) NULL,
`Reliability` varchar(64) NULL,
`Resolution` varchar(64) NULL,
`Priority_Array` varchar(255) NULL,
`Relinquish_Default` varchar(64) NULL,
`Units` varchar(64) NULL,
`Elapsed_Active_Time` varchar(64) NULL,
`Change_Of_State_Count` varchar(64) NULL,
`Polarity` varchar(64) NULL,
`Time_Of_Active_Time_Reset` varchar(64) NULL,
`Active_Text` varchar(255) NULL,
`Inactive_Text` varchar(255) NULL,
`Change_Of_State_Time` varchar(64) NULL,
`Time_Of_State_Count_Reset` varchar(64) NULL,
`Plant` varchar(64) NULL,
`Feedback_Value` varchar(64) NULL,
`Minimum_Off_Time` varchar(64) NULL,
`Minimum_On_Time` varchar(64) NULL,
`Model_Name` varchar(64) NULL,
`Alarm_Value` varchar(64) NULL,
PRIMARY KEY (`id`) 
);

CREATE TABLE `smartio_data_record` (
`id` int UNSIGNED NOT NULL AUTO_INCREMENT,
`device` int UNSIGNED NULL,
`Object_Name` varchar(255) NULL,
`device_instance` varchar(4) NULL,
`device_type` varchar(1) NULL,
`device_number` varchar(2) NULL,
`Present_Value` varchar(255) NULL,
`last_update_time` timestamp NULL,
PRIMARY KEY (`id`) ,
INDEX `key` (`device_instance`)
);

CREATE TABLE `smartio_event` (
`id` int UNSIGNED NOT NULL AUTO_INCREMENT,
`device` int UNSIGNED NULL,
`Description` varchar(255) NULL,
`Object_Name` varchar(255) NULL,
`device_instance` varchar(4) NULL,
`device_type` varchar(1) NULL,
`device_number` varchar(2) NULL,
`Present_Value` varchar(255) NULL,
`last_update_time` timestamp NULL,
`message_number` varchar(64) NULL,
PRIMARY KEY (`id`) ,
INDEX `key` (`device_instance`)
);

CREATE TABLE `smartio_history` (
`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
`tablename` smallint(5) UNSIGNED NOT NULL,
`key1_value` float(10,0) NULL,
`key2_value` float(10,0) NULL,
`key3_value` float(10,0) NULL,
`key4_value` float(10,0) NULL,
`key5_value` float(10,0) NULL,
`key6_value` float(10,0) NULL,
`key7_value` float(10,0) NULL,
`key8_value` float(10,0) NULL,
`last_update_time` timestamp NULL,
PRIMARY KEY (`id`) ,
INDEX `key` ()
);

CREATE TABLE `smartio_history_index` (
`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
`keys` varchar(64) NULL,
`ip` varchar(20) NOT NULL,
`port` varchar(5) NOT NULL,
`tablename` varchar(64) NOT NULL,
`key1` varchar(8) NULL,
`key2` varchar(8) NULL,
`key3` varchar(8) NULL,
`key4` varchar(8) NULL,
`key5` varchar(8) NULL,
`key6` varchar(8) NULL,
`key7` varchar(8) NULL,
`key8` varchar(8) NULL,
PRIMARY KEY (`id`) 
);


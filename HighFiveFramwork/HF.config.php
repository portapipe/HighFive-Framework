<?
# ================== #
# ==== LANGUAGE ==== #
# ================== #
/*
	The file language must be in the lang folder into the HighFive Root!
	Just the lang name, no extensions.
	Editable with any text editor so create your own and share it with everyone!
*/
define('HF_LANGUAGE','IT');
	
	
# ========================= #
# ==== FILES POSITIONS ==== #
# ========================= #
define('__HF__', $_SERVER['DOCUMENT_ROOT']."/HighFiveFramework");

//Lib folder directory (full path)
define("HF_LIB_DIR", __HF__.'/lib/');

//Thirty part files
define("HF_ADDON_DIR",'HighFiveFramework/addon/');
define("HF_FULL_ADDON_DIR",__HF__.'/addon/');

//Set DEBUG MODE! (Lots of messages will popped up!) D=
define("DEBUGMODE", true);


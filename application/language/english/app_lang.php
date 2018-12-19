<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/*listing_model*/
$lang['listing_model.listing_type_id'] = "Room type";
$lang['listing_model.capacity'] = "Number of guest";
$lang["listing_model.description"] = "Description";
$lang['listing_model.email_listing_cretaed_subject'] = "Newer listing has been created";
/**************/

/* ConfigurationModel */
$lang['configuration_model.key_error'] = "The current config value already has been defined";
/***********/

/* ContentModel */
$lang['content_model.key_error'] = "The current value of content already has been defined";
/************/

/* ConfigurationModel */
$lang['listing_type_model.name_error'] = "The current room type already has been defined";
/*******************/

/*User_model*/
$lang["user_model.email"] = "Email";
$lang["user_model.first_name"] = "First name";
$lang["user_model.last_name"] = "Last name";
$lang["user_model.password"] = "Password";
$lang["user_model.email_error"] = "Already existe another user with the same email";
$lang["user_model.not_verificated"] = "Your access acount has not been confirmated. You musk click the link that we sent to your email";
$lang["user_model.verification_token_invalid"] = "The verification token is not correct. You must try to loggin or crete a newer account in authotel.com";


$lang["user_model.email_register_subject"] = "New user account in authotel";
$lang['user_model.email_register_owner_subject'] = "New user registred as owner";
$lang["user_model.mail_recover_password_subject"] = "Recover password in authotel";

$lang['user_model.register_social'] = "You created an account client in Wannbnb.com";

$lang["user_model.email_error"] = "Already exists another user with the same email in authotel.com";
$lang["user_model.username_error"] = "Already existse another user in authotel with the same username";
$lang["user_model.hotel_name_error"] = "Already exists another hotel in authotel with the same name";

$lang['user_model.termns1'] = "The client can cancel the reservation without cost ";
$lang['user_model.termns2'] = ".The client must pay ";
$lang['user_model.termns3'] = " if cancel between ";
$lang['user_model.termns4'] = ".If the client not arrive, he must complete the payment  ";

$lang['user_model.email_play_sales_subject'] = "Start sales";
$lang['user_model.email_stop_sales_subject'] = "Close sales";
$lang['user_model.email_owner_subject'] = "Owner hotel register";

/************/

/*Comment_model*/
$lang['comment_model.content'] = "Content of comment";
/*************/

/*Listing_server_model*/
$lang['listing_reserve_model.payment_description'] = "Payment to authotel.com for the reservation";
$lang['listing_reserve_model.short_description'] = "Reservation in authotel.com";
$lang['listing_reserve_model.not_avalaible']= "The room is not avalaible for the current date";
$lang['listing_reserve_model.email_listing_confirmed_subject']= "The reservation of room has been confirmed";
$lang['listing_reserve_model.is_flexible'] = "The client can cancell the reservation in the next 3 days and recover the amount payed to authotel.com";
$lang['listing_reserve_model.is_flexible_cancelled'] = "The amount payed has been archived for next reservation";
$lang['listing_reserve_model.email_listing_cancelled_subject'] = "The reservation of propery has been cancelled";
$lang['listing_reserve_model.email_listing_review_subject'] = "You recieved an ask review for the reservation";
$lang['listing_reserve_model.ask_review_already'] = "Alreay has been sent a review for current reservation";
$lang['listing_reserve_model.leisure'] = "Leisure";
$lang['listing_reserve_model.work'] = "Work";
$lang['listing_reserve_model.not_avalaible_stope_sales'] = "The room is not avalaible for the current date, because the hotel stopped sales on at least one of the days selected by you to stay at the hotel";

/*Message_model*/
$lang['message_model.content'] = 'Content of message';
/***************/

/*listing_comment_model*/
$lang['listing_comment_model.comment'] = 'Comment';
/**********/

/*listing_rating_model*/
$lang['listing_rating_model.rating'] = 'Calification';
/**********/

$lang['contact_subject'] = "New contact message";

$lang['auth.deny'] = 'The user "%1%" not have autorization for current action';
$lang['auth.no_login'] = 'You must be logged to can do current operation';

$lang['has_reserve'] = "Si";
$lang['has_not_reserve'] = "No";
//$lang['contact_category_
$lang["contact_category_reserve_cancel"] = "Cancelar Reserva";

$lang["contact_category_reserve_change"] = "Cambiar Reserva";
$lang["contact_category_reserve_confirmation"] = "Reenviar Confirmación";
$lang["contact_category_reserve_grantee"] = "Garantía de Reserva";
$lang["contact_category_request_called"] = "Solicitar una Llamada";
$lang["contact_category_reserve_troubles"] = "Problemas con una Reserva";
$lang["contact_category_others"] = "Otros";

$lang['from'] = " From ";
$lang['to'] = " To ";
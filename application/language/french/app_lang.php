<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/*listing_model*/
$lang['listing_model.listing_type_id'] = "Tipo de habitación";
$lang['listing_model.capacity'] = "Cantidad de huespedes";
$lang["listing_model.description"] = "Descripci&oacute;n";
/**************/

/* ConfigurationModel */
$lang['configuration_model.key_error'] = "Este valor de configuraci&oacute;n ya ha sido definido";
/***********/

/* ContentModel */
$lang['content_model.key_error'] = "Este valor de contenido ya ha sido definido";
/************/

/* ConfigurationModel */
$lang['listing_type_model.name_error'] = "Este tipo de habitaci&oacute;n ya ha sido definido";
/*******************/

/*User_model*/
$lang["user_model.email"] = "Correo";
$lang["user_model.first_name"] = "Nombre";
$lang["user_model.last_name"] = "Apellidos";
$lang["user_model.password"] = "Clave de acceso";
$lang["user_model.password"] = "Clave de acceso";
$lang["user_model.email_error"] = "Lo sentimos pero ya existe un usuario con su cuenta de correo";
$lang["user_model.not_verificated"] = "Su cuenta de acceso no ha sido verificada debe dar click en el link que le hemos enviado a su email";
$lang["user_model.verification_token_invalid"] = "El token de verificaci&oacute;n no es correcto, trate de autenticarse o crear una nueva cuenta en Wannbnb";


$lang["user_model.email_register_subject"] = "Nuevo usuario en Wannbnb";
$lang["user_model.mail_recover_password_subject"] = "Clave recuperada en Wannbnb";

$lang['user_model.register_social'] = "Se ha creado una cuenta como cliente en nuestra web Wannbnb.com";

$lang["user_model.email_error"] = "Ya existe un usuario registrado en atuhotel.com con este email";
$lang["user_model.username_error"] = "Ya existe un usuario registrado en atuhotel.com con este nombre de usuario";
/************/

/*Comment_model*/
$lang['comment_model.content'] = "Contenido del comentario";
/*************/

/*Listing_server_model*/
$lang['listing_reserve_model.payment_description'] = "Pago a atuhotel.com por la reservación";
$lang['listing_reserve_model.short_description'] = "Reserva en atuhotel.com";
$lang['listing_reserve_model.not_avalaible']= "La habitaci&oacute;n no est&aacute disponible en para la fecha especificada";
$lang['listing_reserve_model.email_listing_confirmed_subject']= "Se ha confirmado la reserva de la habitaci&oacute;n";
$lang['listing_reserve_model.is_flexible'] = "El cliente puede cancelar la reserva en plazo de 3 dias recuperando el dinero pagado a atuhotel.com";
$lang['listing_reserve_model.is_flexible_cancelled'] = "El dinero pagado a atuhotel.com le ha sido guardado para futuras reservas que realice";
$lang['listing_reserve_model.email_listing_cancelled_subject'] = "Se ha cancelado la reserva de la habitación";
$lang['listing_reserve_model.email_listing_review_subject'] = "Se le ha pedido que de su calificacion sobre una reserva";
$lang['listing_reserve_model.ask_review_already'] = "Esta reserva ya se le ha pedido al cliente que la califique";
$lang['listing_reserve_model.leisure'] = "Leisure";
$lang['listing_reserve_model.work'] = "Work";

/*Message_model*/
$lang['message_model.content'] = 'Contenido del mensaje';
/***************/

/*listing_comment_model*/
$lang['listing_comment_model.comment'] = 'Comentario';
/**********/

/*listing_rating_model*/
$lang['listing_rating_model.rating'] = 'Calificaci&oacute;n';
/**********/



$lang['auth.deny'] = 'Su usuario "%1%" no tiene autorizaci&oacute;n para realizar esta acci&oacute;n. O puede q su secci&oacute;n halla expirado';
$lang['auth.no_login'] = 'Debe estar autenticado para tener acceso a esta opci&oacute;n';

<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/*listing_model*/
$lang['listing_model.listing_type_id'] = "Tipo de habitaci&oacute;n";
$lang['listing_model.capacity'] = "Cantidad de huespedes";
$lang["listing_model.description"] = "Descripci&oacute;n";
$lang['listing_model.email_listing_cretaed_subject'] = "Se ha creado una nueva habitaci&oacute;n";
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
$lang["user_model.first_name"] = "authotel";
$lang["user_model.last_name"] = "Apellidos";
$lang["user_model.password"] = "Clave de acceso";
$lang["user_model.password"] = "Clave de acceso";
$lang["user_model.email_error"] = "Lo sentimos pero ya existe un usuario con su cuenta de correo";
$lang["user_model.not_verificated"] = "Su cuenta de acceso no ha sido verificada debe dar click en el link que le hemos enviado a su email";
$lang["user_model.verification_token_invalid"] = "El token de verificaci&oacute;n no es correcto, trate de autenticarse o crear una nueva cuenta en Authotel";


$lang["user_model.email_register_subject"] = "Nuevo usuario en Authotel";
$lang['user_model.email_register_owner_subject'] = "Nuevo usuario registrado como propietario";
$lang["user_model.mail_recover_password_subject"] = "Clave recuperada en Authotel";

$lang['user_model.register_social'] = "Se ha creado una cuenta como cliente en nuestra web Authotel.com";

$lang["user_model.email_error"] = "Ya existe un usuario registrado en authotel.com con este email";
$lang["user_model.username_error"] = "Ya existe un usuario registrado en authotel.com con este authotel de usuario";
$lang["user_model.hotel_name_error"] = "Ya existe otro hotel en authotel con el mismo nombre";
$lang['user_model.termns1'] = "El cliente puede cancelar la reserva de forma gratuita ";
$lang['user_model.termns2'] = ".El cliente tendrá que pagar ";
$lang['user_model.termns3'] = " si cancela durante los ";
$lang['user_model.termns4'] = ".Si el cliente no se presenta tendrá que pagar ";

$lang['user_model.termns4'] = ".Si el cliente no se presenta tendrá que pagar ";
$lang['user_model.email_play_sales_subject'] = "Restablecimiento de ventas";
$lang['user_model.email_stop_sales_subject'] = "Cierre de ventas";
$lang['user_model.email_owner_subject'] = "Registro como administrador de hotel";
/************/

/*Comment_model*/
$lang['comment_model.content'] = "Contenido del comentario";
/*************/

/*Listing_server_model*/
$lang['listing_reserve_model.payment_description'] = "Pago a authotel.com por la reservación";
$lang['listing_reserve_model.short_description'] = "Reserva en authotel.com";
$lang['listing_reserve_model.not_avalaible'] = "La habitaci&oacute;n no est&aacute disponible en para la fecha especificada";
$lang['listing_reserve_model.email_listing_confirmed_subject'] = "Se ha confirmado la reserva de la habitaci&oacute;n";
$lang['listing_reserve_model.is_flexible'] = "El cliente puede cancelar la reserva en plazo de 3 dias recuperando el dinero pagado a authotel.com";
$lang['listing_reserve_model.is_flexible_cancelled'] = "El dinero pagado a authotel.com le ha sido guardado para futuras reservas que realice";
$lang['listing_reserve_model.email_listing_cancelled_subject'] = "Se ha cancelado la reserva de la habitaci&oacute;n";
$lang['listing_reserve_model.email_listing_review_subject'] = "Se le ha pedido que de su calificacion sobre una reserva";
$lang['listing_reserve_model.ask_review_already'] = "Esta reserva ya se le ha pedido al cliente que la califique";
$lang['listing_reserve_model.leisure'] = "Hocio";
$lang['listing_reserve_model.work'] = "Trabajo";
$lang['listing_reserve_model.not_avalaible_stope_sales'] = "La habitaci&oacute;n no est&aacute disponible en para la fecha especificada,porque el hotel ha detenido las ventas para al menos uno de los dias seleccionado por usted para hospedarse";

/*Message_model*/
$lang['message_model.content'] = 'Contenido del mensaje';
/***************/

/*listing_comment_model*/
$lang['listing_comment_model.comment'] = 'Comentario';
/**********/

/*listing_rating_model*/
$lang['listing_rating_model.rating'] = 'Calificaci&oacute;n';
/**********/

$lang['contact_subject'] = "Nuevo mensaje de contacto";

$lang['auth.deny'] = 'Su usuario "%1%" no tiene autorizaci&oacute;n para realizar esta acci&oacute;n. O puede q su secci&oacute;n halla expirado';
$lang['auth.no_login'] = 'Debe estar autenticado para tener acceso a esta opci&oacute;n';

$lang['bank_model.id'] = "Id. del banco";
$lang['bank_model.id_error'] = "Ya existe otro banco con este identificador";

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

$lang['from'] = " Desde ";
$lang['to'] = " Hasta ";
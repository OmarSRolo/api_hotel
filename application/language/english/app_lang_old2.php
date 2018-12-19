<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$lang['route_model.type_value_walking'] = 'Walking';
$lang['route_model.type_value_in-car'] = 'In Car';

$lang['access.deny'] = 'Your user type not has permission to complete current operation. You must login as %0%';
$lang['auth.email_register_error'] = 'User register error because we can not send notification email';
$lang['auth.email_register.access'] = 'Access application';
$lang['auth.email_register.subject'] = 'New user registered';
$lang['auth.email_register.header'] = 'You created a newer user on the application:';
$lang['auth.email_register.body'] = 'After the administrator approved your account you can access to application using the info:';


$lang['auth.email_approved.subject'] = 'Account approved';
$lang['auth.email_approved.header'] = 'Your account in '.COMPANY. ' has been approved by admin';
$lang['auth.email_approved.body'] = 'Please feel free to enter to our system and edit your profile if it is your desire';

$lang['auth.email_pass_recovered.subject'] = 'Password recovered';
$lang['auth.email_pass_recovered.header'] = 'The system generated a newer password required by you, feel free it to change it, on your profile';
$lang['auth.email_pass_recovered.body'] = '<hr><h4>Details of user:</h4><p>Username: %0% </p><p>Password: %1% </p>';

$lang['auth.password.incorrect_password'] = 'The old password is not correct';
$lang['auth.password.label_new_password'] = 'New password';



$lang['log.system_login'] = 'Login to system: %0%';
$lang['log.system_login_error'] = 'Login error to system: %0%';
$lang['log.user_created'] = 'User # %0% registered, username: %1%';
$lang['log.user_edited'] = 'User # %0% updated, username: %1%';
$lang['log.user_deleted'] = 'User # %0% deleted, username: %1%';

/****Training****/
$lang['log.training_created'] = 'Training # %0% registered, sport: %1%';
$lang['log.training_edited'] = 'Training # %0% updated, sport: %1%';
$lang['log.training_deleted'] = 'Training # %0% deleted, sport: %1%';

$lang['training.sport_label'] = 'Sport name';
$lang['training.date_label'] = 'Date of training';
$lang['training.distance_label'] = 'Distance';
$lang['training.duration_label'] = 'Time duration';
$lang['training.error.duplicated'] = 'Workouts is already registered in the system';
$lang['training.error.date_invalid'] = 'You can not insert a workout with more than 7 days of behind that current time';
$lang['training.error.like_exists'] = 'You can not doing this action more that one time';

/****Comments****/
$lang['comment.content'] = 'Content of comment';
$lang['comment.user_id'] = 'Writer of comment';
$lang['comment.error.like_exists'] = 'You can not doing this action more that one time';
/****End Comments****/

/****Messages****/
$lang['message.subject'] = 'Subject of message';
$lang['message.content'] = 'Content of message';
$lang['message.writer_id'] = 'Writer of message';

$lang['log.message_created'] = 'Message # %0% wrote by the user : %1%';
$lang['log.message_edited']  = 'Message # %0% wrote by the user : %1% has been edited';
$lang['log.message_deleted'] = 'Message # %0% wrote by the user : %1% has been deleted';

/****End Messages****/

$lang['post.content'] = 'Content of post';
$lang['post.user_id'] = 'Writer of post';
$lang['post.error.like_exists'] = 'You can not doing this action more that one time';

$lang['log.comment_created'] = 'Comment wrote by the user %0% for the workout of type: %1%';
$lang['log.comment_edited']  = 'Comment edited by the user %0% for the workout of type: %1%';;
$lang['log.comment_deleted'] = 'Comment deleted by the user %0% for the workout of type: %1%';

$lang['log.post_created'] = 'Post wrote by the user %0% for the workout of type: %1%';
$lang['log.post_edited']  = 'Post edited by the user %0% for the workout of type: %1%';;
$lang['log.post_deleted'] = 'Post deleted by the user %0% for the workout of type: %1%';

$lang['log.training_like_created'] = 'The user %0% like the workout of type: %1%';
$lang['log.comment_like_created'] = 'The user %0% like the comment #: %1%';
$lang['log.post_like_created'] = 'The user %0% like the post #: %1%';



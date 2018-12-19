<?php



function lang_extension($line, $vars = array())

{

    get_instance()->lang->load('app');

    

    $line = get_instance()->lang->line($line);



    $n = 0;

    foreach ($vars as $v)

    {

        $line = str_replace('%'.$n.'%', $v, $line);

        $n++;

    }





    return $line;

}


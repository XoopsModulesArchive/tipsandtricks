<?php
//  ------------------------------------------------------------------------ //
//                      Tips and Tricks Module for                           //
//               XOOPS - PHP Content Management System 2.0                   //
//                            Versión 1.0.0                                  //
//                   Copyright (c) 2004 Daniel Halberg                       //
//                       http://www.guitargearheads.com                      //
// ------------------------------------------------------------------------- //

/******************************************************************************
 * Function: random_tip_show
 * Input   : void
 * Output  : $texto: Text of the quote
             $autor: Autor of the quote
 ******************************************************************************/
function random_tip_show()
{
    global $xoopsDB;

    $block = [];

    $result = $xoopsDB->query('SELECT texto, autor FROM ' . $xoopsDB->prefix('tipstricks') . ' ORDER BY RAND() LIMIT 1');

    [$texto, $autor] = $xoopsDB->fetchRow($result);

    $block['texto'] = $texto;

    $block['autor'] = $autor;

    return $block;
}

<?php
//  ------------------------------------------------------------------------ //
//                      Random Quotes Module for                             //
//               XOOPS - PHP Content Management System 2.0                   //
//                            Versión 1.0.0                                  //
//                   Copyright (c) 2004 Daniel Halberg                       //
//                       http://www.guitargearheads.com                      //
// ------------------------------------------------------------------------- //

require_once 'admin_header.php';

$op = 'list';

if (isset($_GET)) {
    foreach ($_GET as $k => $v) {
        $$k = $v;
    }
}

if (isset($_POST)) {
    foreach ($_POST as $k => $v) {
        $$k = $v;
    }
}

if (!empty($contents_preview)) {
    $myts = MyTextSanitizer::getInstance();

    xoops_cp_header();

    $html = empty($nohtml) ? 1 : 0;

    $smiley = empty($nosmiley) ? 1 : 0;

    $xcode = empty($noxcode) ? 1 : 0;

    $p_title = htmlspecialchars($album, ENT_QUOTES | ENT_HTML5);

    $p_contents = $myts->previewTarea($comentario, $html, $smiley, $xcode);

    echo "<table border='0' cellpadding='0' cellspacing='0' width='100%'><tr><td class='bg2'>
    <table width='100%' border='0' cellpadding='4' cellspacing='1'>
    <tr class='bg3' align='center'><td align='left'>$p_title</td></tr><tr class='bg1'><td>$p_contents</td></tr></table></td></tr></table><br>";

    $album = htmlspecialchars($album, ENT_QUOTES | ENT_HTML5);

    $comentario = htmlspecialchars($comentario, ENT_QUOTES | ENT_HTML5);

    include 'contentsform.php';

    xoops_cp_footer();

    exit();
}

if ('list' == $op) {
    // List quoete in database, and form for add new.

    $myts = MyTextSanitizer::getInstance();

    xoops_cp_header();

    echo "
    <h4 style='text-align:left;'>" . _RQ_TITLE . "</h4>
    <form action='index.php' method='post'>
    <table border='0' cellpadding='0' cellspacing='0' width='100%'><tr><td class='bg2'>
    <table width='100%' border='0' cellpadding='4' cellspacing='1'>
    <tr class='bg3' align='center'><td align='left'>" . _RQ_TEXTO . '</td><td>' . _RQ_AUTOR . '</td><td>&nbsp;</td></tr>';

    $result = $xoopsDB->query('SELECT * FROM ' . $xoopsDB->prefix('tipstricks'));

    $count = 0;

    while (list($id, $texto, $autor) = $xoopsDB->fetchRow($result)) {
        $texto = htmlspecialchars($texto, ENT_QUOTES | ENT_HTML5);

        $autor = htmlspecialchars($autor, ENT_QUOTES | ENT_HTML5);

        echo "<tr class='bg1'><td align='left'>
            <input type='hidden' value='$id' name='id[]'>
            <input type='hidden' value='$texto' name='oldtexto[]'>
            <textarea name='newtexto[]' rows='2'>$texto</textarea>
            </td>
        <td align='center'>
            <input type='hidden' value='$autor' name='oldautor[]'>
            <input type='text' value='$autor' name='newautor[]' maxlength='255' size='20'>
        </td>
        <td nowrap='nowrap' align='right'><a href='index.php?op=del&amp;id=" . $id . "&amp;ok=0'>" . _DELETE . '</a></td></tr>';

        $count++;
    }

    if ($count > 0) {
        echo "<tr align='center' class='bg3'><td colspan='4'><input type='submit' value='" . _SUBMIT . "'><input type='hidden' name='op' value='edit'></td></tr>";
    }

    echo "</table></td></tr></table></form>
    <br><br>
    <h4 style='text-align:left;'>" . _RQ_ADD . "</h4>
    <form action='index.php' method='post'>
    <table border='0' cellpadding='0' cellspacing='0' width='100%'>
        <tr>
        <td class='bg2'>
            <table width='100%' border='0' cellpadding='4' cellspacing='1'>
                <tr nowrap='nowrap'>
                <td class='bg3'>" . _RQ_AUTOR . " </td>
                <td class='bg1'>
                    <input type='text' name='autor' size='30' maxlength='255'>
                </td></tr>
                <tr nowrap='nowrap'>
                <td class='bg3'>" . _RQ_TEXTO . " </td>
                <td class='bg1'>
                    <textarea name='texto' cols='20' rows='3'></textarea>
                </td></tr>
                <tr>
                <td class='bg3'>&nbsp;</td>
                <td class='bg1'>
                    <input type='hidden' name='op' value='add'>
                    <input type='submit' value='" . _SUBMIT . "'>
                </td></tr>
            </table>
        </td></tr>
    </table>
    </form>";

    xoops_cp_footer();

    exit();
}

if ('add' == $op) {
    // Add quote

    $myts = MyTextSanitizer::getInstance();

    $artista = $myts->addSlashes($autor);

    $texto = $myts->addSlashes($texto);

    $newid = $xoopsDB->genId($xoopsDB->prefix('tipstricks') . 'id');

    $sql = 'INSERT INTO ' . $xoopsDB->prefix('tipstricks') . ' (id, autor, texto) VALUES (' . $newid . ", '" . $autor . "', '" . $texto . "')";

    if (!$xoopsDB->query($sql)) {
        xoops_cp_header();

        echo 'Could not add category';

        xoops_cp_footer();
    } else {
        redirect_header('index.php?op=list', 1, _XD_DBSUCCESS);
    }

    exit();
}

if ('edit' == $op) {
    // Edit quotes

    $myts = MyTextSanitizer::getInstance();

    $count = count($newautor);

    for ($i = 0; $i < $count; $i++) {
        if ($newautor[$i] != $oldautor[$i] || $newtexto[$i] != $oldtexto[$i]) {
            $autor = $myts->addSlashes($newautor[$i]);

            $texto = $myts->addSlashes($newtexto[$i]);

            $sql = 'UPDATE ' . $xoopsDB->prefix('tipstricks') . " SET autor='" . $autor . "',texto='" . $texto . "' WHERE id=" . $id[$i] . '';

            $xoopsDB->query($sql);
        }
    }

    redirect_header('index.php?op=list', 1, _XD_DBSUCCESS);

    exit();
}

if ('del' == $op) {
    // Delete quote

    if (1 == $ok) {
        $sql = 'DELETE FROM ' . $xoopsDB->prefix('tipstricks') . ' WHERE id = ' . $id;

        if (!$xoopsDB->query($sql)) {
            xoops_cp_header();

            echo 'Could not delete category';

            xoops_cp_footer();
        } else {
            redirect_header('index.php?op=list', 1, _XD_DBSUCCESS);
        }

        exit();
    }  

    xoops_cp_header();

    xoops_confirm(['op' => 'del', 'id' => $id, 'ok' => 1], 'index.php', _RQ_SUREDEL);

    xoops_cp_footer();

    exit();
}

<?php

require_once(dirname(__FILE__) . '/../vendor/autoload.php');

use MongoDB;

function GETPOST($paramname)
{
    if (isset($_POST[$paramname]))
        return $_POST[$paramname];
    if (isset($_GET[$paramname]))
        return $_GET[$paramname];
    return false;
}

function GETPOSTISSET($paramname)
{
    return (isset($_POST[$paramname]) || isset($_GET[$paramname]));
}


function print_tr_movie($document, $cols)
{
    $elt = secure_document($document, $cols);
    print '<tr>';
    foreach ($cols as $key => $dtls) { ?>
        <td>
            <?php
            if ($dtls['type'] == 'id') {
                echo '<a href="index.php?action=edit&id=' . $elt[$key] . '">';
                echo '<i class="fas fa-edit w3-hover-opacity" aria-hidden="true"></i>';
                echo '</a>';
            } elseif ($dtls['type'] == 'textarea') {
                print nl2br($elt[$key] ?? '');
            } elseif ($dtls['type'] == 'array') {
                print implode('<br />', $elt[$key]);
            } else {
                echo $elt[$key];
            }
            ?>
        </td>
<?php
    }
    print '<td>';
    echo '<a href="index.php?action=delete&id=' . $elt[$key] . '">';
    echo '<i class="fas fa-trash w3-hover-opacity" aria-hidden="true"></i>';
    echo '</a>';
    print '</td>';
    print '</tr>';
}

function merge_dtls($doc, $dtls, $cast) {
    if (isset($dtls['production_companies']) && is_array($dtls['production_companies'])) {
        $final_prods = array_map(function($company) { return $company['name']; }, $dtls['production_companies']);
        $doc['production'] = implode(PHP_EOL, $final_prods);
    } else {
        $doc['production'] = 'Information non disponible';
    }

    if (isset($cast['cast']) && is_array($cast['cast'])) {
        $final_cast = array_map(function($actor) { return $actor['name']; }, array_slice($cast['cast'], 0, 5));
        $doc['actors'] = implode(PHP_EOL, $final_cast);
    } else {
        $doc['actors'] = 'Information non disponible';
    }

    return $doc;
}


function secure_document($elt, $cols)
{
    //    print_r($elt);
    foreach ($elt as $i_elt => $val_elt) {
        if (is_object($val_elt)) {
            $classname = get_class($val_elt);
            switch ($classname) {
                case 'MongoDB\BSON\ObjectId':
                    $elt[$i_elt] = $val_elt->__toString();
                    // var_dump($elt[$i_elt]);
                    break;
                case 'MongoDB\Model\BSONArray':
                    $elt[$i_elt] = (array)$val_elt;
                    // var_dump($elt[$i_elt]);
                    break;
                default;
                    // print 'Obj : '.var_dump($classname) . '<br />';
                    break;
            }
        } else {
            //          print 'Oth : '.gettype($val_elt) . '<br />';
        }
    }
    // print '<hr />';
    // print_r($elt);
    // print '<hr />';
    foreach ($cols as $key => $dtls) {
        if (isset($elt[$key])) {
            switch ($dtls['type']) {
                case 'array':
                    $elt[$key] = (is_array($elt[$key]) ? $elt[$key] : (array)$elt[$key]);
                    break;
                default:
                    break;
            }
        } else {
            if ($dtls['type'] === 'array') {
                $elt[$key] = [];
            } else {
                $elt[$key] = null;
            }
        }
    }
    
    return $elt;
}

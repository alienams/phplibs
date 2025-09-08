<?php 

function convert_id_2name($id)
{
    switch ( $id )
    {
        case 1:
            $cname = "jan";
            break;
        case 2:
            $cname = "feb";
            break;
        case 3:
            $cname = "mar";
            break;
        case 4:
            $cname = "apr";
            break;
        
        case 5:
            $cname = "mei";
            break;
        case 6:
            $cname = "jun";
            break;
        case 7:
            $cname = "jul";
            break;
        case 8:
            $cname = "ags";
            break;
    
        case 9:
            $cname = "sep";
            break;
        case 10:
            $cname = "okt";
            break;
        case 11:
            $cname = "nov";
            break;
        case 12:
            $cname = "des";
            break;
        default:
        $cname = " ";
        break;
    }

    return  $cname;
}

function convert_id_2month($id)
{
    switch ( $id )
    {
        case 1:
            $cname = "JANUARI";
            break;
        case 2:
            $cname = "FEBRUARI";
            break;
        case 3:
            $cname = "mar";
            break;
        case 4:
            $cname = "apr";
            break;
        
        case 5:
            $cname = "mei";
            break;
        case 6:
            $cname = "jun";
            break;
        case 7:
            $cname = "jul";
            break;
        case 8:
            $cname = "ags";
            break;
    
        case 9:
            $cname = "sep";
            break;
        case 10:
            $cname = "okt";
            break;
        case 11:
            $cname = "nov";
            break;
        case 12:
            $cname = "des";
            break;
        default:
        $cname = " ";
        break;
    }

    return  $cname;
}

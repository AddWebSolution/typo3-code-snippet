<?php

namespace PlusItde\SoTypo3\UserFunctions\ExtensionConfiguration;



/**
 * shortcode function 1
 */
class Shortcode
{

    public function shortcode_1()
    {
        $out = ' <ul>';
        $out .= '<li><b>Show Trainer List : </b> [trainerList=]</li>';
        $out .= '</ul>';
        return $out;

    }
}

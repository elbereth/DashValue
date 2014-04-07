<?php
/*
Darkcoin Value for Mediawiki

The MIT License (MIT)

Copyright (c) 2014 Alexandre Devilliers

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/

if ( !defined( 'MEDIAWIKI' ) ) {
        die( 'This file is a MediaWiki extension, it is not a valid entry point' );
}


$messages = array();

/** English
 * @author Alexandre Devilliers
 */
$messages['en'] = array(
        'darkcoinvalues-desc' => 'Adds &lt;darkcoinvalue [value=todisplay] /&gt; to retrieve various DRK (Darkcoin) values.'
);

/** Spanish (Español)
 * @author Alexandre Devilliers
 */
$messages['es'] = array(
        'darkcoinvalues-desc' => 'Añade &lt;darkcoinvalue [value=todisplay] /&gt; para récuperar valores de DRK (Darkcoin).'
);

/** French (Français)
 * @author Alexandre Devilliers
 */
$messages['fr'] = array(
        'darkcoinvalues-desc' => 'Ajoute &lt;darkcoinvalue [value=a_afficher] /&gt; pour récupérer différentes valeurs de DRK (Darkcoin).'
);


?>

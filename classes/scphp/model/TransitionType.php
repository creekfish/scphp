<?php

namespace scphp\model;

/**
 * The type of an SCXML transition.
 * SXCML Version: http://www.w3.org/TR/2012/WD-scxml-20121206/ [WD6]
 *
 * NOTE: as of W3C Working Draft 6, transition type attribute could be
 * either 'external' or 'internal'.  For an explanation of the difference
 * (which is non-trivial), refer to the SXCML W3C recommendation.
 *
 * @see http://www.w3.org/TR/scxml/#CoreIntroduction
 *
 *
 * @author bherring
 */
final class TransitionType
{
    const Internal = 0;
    const External = 1;

    private function __construct()
    {

    }
}

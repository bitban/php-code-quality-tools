<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools\Interfaces;


interface ValidatorInterface
{
    const SEVERITY_WARNING = 'warning';
    const SEVERITY_ERROR = 'error';
        
    /** @return bool */
    public function validate();
}

<?php

namespace Umbrella\WkToPdf;

/**
 * Defines a generic render method.
 * @author Ítalo Lelis de Vietro <italo@voxtecnologia.com.br>
 */
interface IRenderer
{

    /**
     * Writes the content to be rendered on the client.
     */
    public function render();
}

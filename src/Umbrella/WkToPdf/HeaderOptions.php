<?php

namespace Umbrella\WkToPdf;

/**
 * Description of HtmlFooterRenderer
 *
 * @author vox
 */
class HeaderOptions extends \Umbrella\WkToPdf\AbstractOptions
{

    /**
     * Pega o footer para ser utilizado no \wkhtmltox_convert()
     * @return array
     */
    public function getOptions()
    {
        $options = new \Easy\Collections\Dictionary();

        $text = implode('<br />', $this->text);
        fopen($this->path, 'a');
        chmod($this->path, 0755);
        $text = preg_replace(
                array('#\$\{TEXTO\}#i', '#\$\{([^\}]+)\}#ie'), array($text, "'<span class=\"'.strtolower('$1').'\">'.strtolower('$1').'</span>'"), $this->appendScript(file_get_contents($this->pathTemplate))
        );
        file_put_contents($this->path, $text);
        $options->add('header.htmlUrl', $this->htmlUrl);

        return $options;
    }

}

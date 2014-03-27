<?php

namespace Umbrella\WkToPdf;

/**
 * Description of HtmlFooterRenderer
 *
 * @author vox
 */
class AbstractOptions
{

    /**
     * @var string 
     */
    protected $pathTemplate;

    /**
     * @var string 
     */
    protected $path;

    /**
     * @var string 
     */
    protected $htmlUrl;

    /**
     * @var array 
     */
    protected $text = array();

    public function getPathTemplate()
    {
        return $this->pathTemplate;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getHtmlUrl()
    {
        return $this->htmlUrl;
    }

    public function getText()
    {
        return $this->text;
    }

    public function setPathTemplate($pathTemplate)
    {
        $this->pathTemplate = $pathTemplate;
        return $this;
    }

    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    public function setHtmlUrl($htmlUrl)
    {
        $this->htmlUrl = $htmlUrl;
        return $this;
    }

    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    /**
     * Adiciona o script para gerar as variáveis dinamicas
     * 
     * @param string $text
     * @return mixed
     */
    protected function appendScript($text)
    {
        $script = '<script>function subst() {var vars={};var x=document.location.search.substring(1).split(\'&\');';
        $script .= 'for(var i in x) {var z=x[i].split(\'=\',2);vars[z[0]] = unescape(z[1]);}var x=[\'frompage\',\'topage\',\'page\',';
        $script .= '\'webpage\',\'section\',\'subsection\',\'subsubsection\']; for(var i in x) { var y = document.getElementsByClassName(x[i]);';
        $script .= 'for(var j=0; j<y.length; ++j) y[j].textContent = vars[x[i]];}}</script></head><body onload="subst()">';
        return preg_replace('#\<\/head\>([^\<]+|)\<body([^\>]+|)\>#im', $script, $text);
    }

}

<?php

/*
 * Copyright 2014 kelsoncm <falecom@kelsoncm.com>.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Umbrella\WkToPdf;

use Easy\Collections\Dictionary;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Classe utilizada para gerar relatórios em PDF com o wkhtmltopdf
 * @author Ítalo Lelis <italo@voxtecnologia.com.br>
 */
class WkPdfRenderer implements IRenderer
{

    /**
     * @var Filesystem
     */
    protected $fs;

    /**
     * @var string 
     */
    private $pdfName;

    /**
     * @var string 
     */
    private $pdfPath;

    /**
     * @var string 
     */
    private $htmlPath;

    /**
     * @var string 
     */
    private $htmlContent;

    /**
     * @var Dictionary
     */
    private $options;

    /**
     * @var Dictionary
     */
    private $pdfOptions;

    /**
     * @var boolean 
     */
    private $isStreaming = false;

    /**
     * @var FooterOptions 
     */
    private $footer;

    /**
     * @var HeaderOptions 
     */
    private $header;

    /**
     * Inicializa uma nova instancia da classe WkPdfRenderer
     * @param IDatasource $datasource Uma instância de IDatasource
     * @param ITemplate $template Uma instância de ITemplate
     */
    public function __construct()
    {
        if (!function_exists("wkhtmltox_convert")) {
            throw new WkExtensionNotInstalledException("A extensão do wkhtmltox não está instalada");
        }

        $this->fs = new Filesystem();
        $this->options = new Dictionary();
        $this->pdfOptions = new Dictionary();
    }

    public function getPdfName()
    {
        return $this->pdfName;
    }

    public function getHtmlPath()
    {
        return $this->htmlPath;
    }

    public function getHtmlContent()
    {
        return $this->htmlContent;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function getPdfOptions()
    {
        return $this->pdfOptions;
    }

    public function getFooter()
    {
        return $this->footer;
    }

    public function getHeader()
    {
        return $this->header;
    }

    public function setPdfName($pdfName)
    {
        $this->pdfName = $pdfName;
        return $this;
    }

    public function setHtmlPath($htmlPath)
    {
        $this->htmlPath = $htmlPath;
        return $this;
    }

    public function setHtmlContent($htmlContent)
    {
        $this->htmlContent = $htmlContent;
        return $this;
    }

    public function setOptions(Dictionary $options)
    {
        $this->options = $options;
        return $this;
    }

    public function setPdfOptions(Dictionary $pdfOptions)
    {
        $this->pdfOptions = $pdfOptions;
        return $this;
    }

    public function setFooter(FooterOptions $footer)
    {
        $this->footer = $footer;
        return $this;
    }

    public function setHeader(HeaderOptions $header)
    {
        $this->header = $header;
        return $this;
    }

    /**
     * Define o caminho do PDF a ser gerado
     * @return string
     */
    public function setPdfPath($pdfPath)
    {
        $this->pdfPath = $pdfPath;
        return $this;
    }

    /**
     * Recupera o caminho do PDF gerado
     * @return string
     */
    public function getPdfPath()
    {
        return $this->pdfPath;
    }

    /**
     * Verifica se o relatório será renderizado por streaming
     * @return type
     */
    public function isStreaming()
    {
        return $this->isStreaming;
    }

    /**
     * Define se o relatório será renderizado por streaming
     * @param boolean $isStreaming
     * @return WkPdfRenderer
     */
    public function setStreaming($isStreaming)
    {
        $this->isStreaming = $isStreaming;
        return $this;
    }

    /**
     * Renderiza o PDF
     * @return WkPdfRenderer
     */
    public function render()
    {
        $htmlFile = $this->htmlContent;

        if ($this->isStreaming()) {
            $response = new StreamedResponse();

            ob_start();
            $self = $this;

            $response->setCallback(function () use($htmlFile, $self, &$lenght) {
                $self->createPdf($htmlFile);
                ob_flush();
                flush();
            });
        } else {
            $response = new Response();
            $this->createPdf($htmlFile);
        }

        $this->send($response);

        return $this;
    }

    protected function send(Response $response)
    {
        $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_INLINE, $this->pdfName);
        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-type', 'application/pdf');
        $response->headers->set('Cache-Control', 'max-age=0, must-revalidate');
        $response->headers->set('Pragma', 'public');

        $response->send();
    }

    /**
     * Converte um HTML em PDF. Esse método deve ser publico pois quando é feito o streaming ele precisa ser acessível.
     * @param string $htmlContent O conteúdo HTML para ser convertido
     */
    public function createPdf($htmlContent)
    {
        $htmlFile = $this->createHtmlFile($htmlContent);
        $footerOptions = array();
        $headerOptions = array();
        if ($this->footer) {
            $footerOptions = $this->footer->getOptions();
        }

        if ($this->header) {
            $headerOptions = $this->header->getOptions();
        }

        $this->options->set('out', $this->pdfPath . '/' . $this->pdfName . '.pdf');
        $this->options->set('imageQuality', '75');
        $this->pdfOptions->set('page', "file://{$htmlFile}");
        $this->pdfOptions->addAll($footerOptions);
        $this->pdfOptions->addAll($headerOptions);

        \wkhtmltox_convert('pdf', $this->options->toArray(), array(
            $this->pdfOptions->toArray()
        ));

        $this->setPermissions(array(
            $this->pdfPath . '/' . $this->pdfName . '.pdf',
            $this->htmlPath
        ));

        return readfile($this->pdfPath . '/' . $this->pdfName . '.pdf');
    }

    /**
     * Recupera o conteúdo HTML do template. O método faz o parse do HTML e retorna o conteúdo pronto.
     * @return string O HTML final após o parse
     */
    protected function createHtmlFile($htmlContent)
    {
        if (!$this->htmlPath) {
            $this->htmlPath = '/tmp/' . microtime() . '.html';
        }
        $this->fs->dumpFile($this->htmlPath, $htmlContent);
        return $this->htmlPath;
    }

    /**
     * Seta as permissões nos arquivos de html e pdf
     * @param array $file
     */
    protected function setPermissions(array $file)
    {
        return $this->fs->chmod($file, 0777);
    }

}

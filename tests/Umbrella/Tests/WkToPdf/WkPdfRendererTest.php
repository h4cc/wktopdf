<?php

namespace Umbrella\Tests\WkToPdf;

/**
 * Description of SimpleTest
 *
 * @author Ítalo Lelis <italo@voxtecnologia.com.br>
 */
class WkPdfRendererTest extends \PHPUnit_Framework_TestCase
{

    public function htmlProvider()
    {
        return array(
            array('<html><head></head><body><h1>TESTE</h1></body></html>')
        );
    }

    /**
     * @dataProvider htmlProvider
     */
    public function testBasicRender($htmlContent)
    {
        $options = new \Easy\Collections\Dictionary();
        $options->add('test', 'teste');

        $renderer = new \Umbrella\WkToPdf\WkPdfRenderer();
        $renderer->setHtmlContent($htmlContent)
                ->setHtmlPath(__DIR__ . '/../../../tmp/wktopdf.html')
                ->setPdfName('wktopdf')
                ->setPdfPath('/var/www/wktopdf/tests/tmp')
                ->setOptions($options)
                ->setPdfOptions($options);

        $this->assertNotNull($renderer->getHtmlContent());
        $this->assertNotNull($renderer->getHtmlPath());
        $this->assertNotNull($renderer->getPdfName());
        $this->assertNotNull($renderer->getPdfPath());
        $this->assertNotNull($renderer->getOptions());
        $this->assertNotNull($renderer->getPdfOptions());

        $renderer->render();
    }

    /**
     * @dataProvider htmlProvider
     */
    public function testNoHtmlPathProvided($htmlContent)
    {
        $renderer = new \Umbrella\WkToPdf\WkPdfRenderer();
        $renderer->setHtmlContent($htmlContent)
                ->setPdfName('wktopdf')
                ->setPdfPath('/var/www/wktopdf/tests/tmp');


        $renderer->render();
    }

    /**
     * @dataProvider htmlProvider
     */
    public function testStreamingPdf($htmlContent)
    {
        $renderer = new \Umbrella\WkToPdf\WkPdfRenderer();
        $renderer->setHtmlContent($htmlContent)
                ->setPdfName('wktopdf')
                ->setPdfPath('/var/www/wktopdf/tests/tmp')
                ->setStreaming(true)
        ;

        $renderer->render();
    }

    /**
     * @dataProvider htmlProvider
     */
    public function testFooterRender($htmlContent)
    {
        $footer = new \Umbrella\WkToPdf\FooterOptions();
        $footer->setPathTemplate(__DIR__ . '/../../../footer.html')
                ->setHtmlUrl('file:///var/www/wktopdf/tests/footer.html')
                ->setPath(__DIR__ . '/../../../tmp/footer.html')
                ->setText(array('Teste Footer'));

        $renderer = new \Umbrella\WkToPdf\WkPdfRenderer();
        $renderer->setHtmlContent($htmlContent)
                ->setHtmlPath(__DIR__ . '/../../../tmp/wktopdf-footer.html')
                ->setPdfName('wktopdf-footer')
                ->setPdfPath('/var/www/wktopdf/tests/tmp')
                ->setFooter($footer);

        $renderer->render();
    }

    /**
     * @dataProvider htmlProvider
     */
    public function testHeaderRender($htmlContent)
    {
        $footer = new \Umbrella\WkToPdf\HeaderOptions();
        $footer->setPathTemplate(__DIR__ . '/../../../header.html')
                ->setHtmlUrl('file:///var/www/wktopdf/tests/header.html')
                ->setPath(__DIR__ . '/../../../tmp/header.html')
                ->setText(array('Teste Header'));

        $renderer = new \Umbrella\WkToPdf\WkPdfRenderer();
        $renderer->setHtmlContent($htmlContent)
                ->setHtmlPath(__DIR__ . '/../../../tmp/wktopdf-header.html')
                ->setPdfName('wktopdf-header')
                ->setPdfPath('/var/www/wktopdf/tests/tmp')
                ->setHeader($footer);

        $renderer->render();
    }

    /**
     * @dataProvider htmlProvider
     */
    public function testHeaderAndFooterRender($htmlContent)
    {
        $header = new \Umbrella\WkToPdf\HeaderOptions();
        $header->setPathTemplate(__DIR__ . '/../../../header.html')
                ->setHtmlUrl('file:///var/www/wktopdf/tests/header.html')
                ->setPath(__DIR__ . '/../../../tmp/header.html')
                ->setText(array('Teste Header'));

        $footer = new \Umbrella\WkToPdf\FooterOptions();
        $footer->setPathTemplate(__DIR__ . '/../../../footer.html')
                ->setHtmlUrl('file:///var/www/wktopdf/tests/footer.html')
                ->setPath(__DIR__ . '/../../../tmp/footer.html')
                ->setText(array('Teste Footer'));

        $renderer = new \Umbrella\WkToPdf\WkPdfRenderer();
        $renderer->setHtmlContent($htmlContent)
                ->setHtmlPath(__DIR__ . '/../../../tmp/wktopdf-header-footer.html')
                ->setPdfName('wktopdf-header-footer')
                ->setPdfPath('/var/www/wktopdf/tests/tmp')
                ->setHeader($header)
                ->setFooter($footer);

        $this->assertNotNull($header->getHtmlUrl());
        $this->assertNotNull($header->getPath());
        $this->assertNotNull($header->getPathTemplate());
        $this->assertNotNull($header->getText());

        $this->assertNotNull($renderer->getFooter());
        $this->assertNotNull($renderer->getHeader());

        $renderer->render();
    }

}

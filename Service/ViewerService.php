<?php

namespace jjalvarezl\PDFjsViewerBundle\Service;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class ViewerService
{
    /** @var Environment */
    private $twig;

    public function __construct(Environment $twig) {
        $this->twig = $twig;
    }

    /**
     * Solves de pdf final location under the restriction that pdf's viewers can be only seen in
     * webroot directories.
     *
     * @param boolean $isPdfOutsideWebroot , determines if pdf is inside or outside webroot
     * @param $pdf , the path of the pdf, depends of $isPdfOutsideWebroot, if it's true, $pdf is absolute, else just the pdf complete name.
     * @param $tmpPdfPath , path of pdf's temporal dir
     * @param $projectDir , project directory
     * @return string final pdf name, it already is inside the temp dir of pdfs
     * @throws Exception
     */
    private function solvePDFLocation(bool $isPdfOutsideWebroot, $pdf, $tmpPdfPath, $projectDir): string
    {
        if(!$tmpPdfPath){
            $tmpPdfPath = '/bundles/jjalvarezlpdfjsviewer/tmpPdf/';
        }

        if($isPdfOutsideWebroot){
            exec('cp '.$pdf.' '.$projectDir.'public'.$tmpPdfPath.' 2>&1', $output, $returnVal);
            if($returnVal!=0){
                throw new Exception('Can not copy pdf file to temporal directory: Exit='.$returnVal.' Message: '.implode(' ',$output));
            }
            $splittedPDFRoute = explode('/', $pdf);
            $pdf = end($splittedPDFRoute);
        }
        return $pdf;
    }

    /**
     * Renders the default pdf (used for test viewer with default settings).
     *
     * Mostly used for testing this viewer with all browsers.
     *
     */
    public function renderTestViewer(): Response
    {
        return new Response($this->twig->render('@jjalvarezlPDFjsViewer/viewer/default.html.twig'));
    }

    /**
     * View a pdf with all visual elements but custom pdf location (inside or outside webroot)
     *
     * an example of custom parameters customizing the viewer's options.
     * @param $parameters
     * @return Response
     * @throws Exception
     */
    public function renderDefaultViewer($parameters): Response
    {

        $parameters['pdf'] = $this->solvePDFLocation(
            $parameters['isPdfOutsideWebroot'],
            $parameters['pdf'],
            !isset($parameters['tmpPdfDirectory'])? null : $parameters['tmpPdfDirectory'],
            !isset($parameters['projectDir'])? null : $parameters['projectDir']
        );
        return new Response($this->twig->render('@jjalvarezlPDFjsViewer/viewer/default.html.twig',
            $parameters
        ));
    }

    /**
     * View a pdf with custom parameters showing or hiding the pdf's viewer elements.
     *
     * an example of custom parameters customizing the viewer's options.
     *
     */
    public function renderCustomViewer($parameters): Response
    {
        $parameters['pdf'] = $this->solvePDFLocation(
            $parameters['isPdfOutsideWebroot'],
            $parameters['pdf'],
            !isset($parameters['tmpPdfDirectory'])? null : $parameters['tmpPdfDirectory'],
            !isset($parameters['projectDir'])? null : $parameters['projectDir']
        );
        return new Response($this->twig->render('@jjalvarezlPDFjsViewer/viewer/custom.html.twig',
            $parameters
        ));
    }
}

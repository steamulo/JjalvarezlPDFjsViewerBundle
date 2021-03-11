<?php

namespace jjalvarezl\PDFjsViewerBundle\Controller;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ViewerController extends AbstractController
{

    /**
     * Pdf system deletion from tmpDir
     *
     * @param Request $request
     * @return Response, Error in pdf deletion. If it's ok, this function must return an empty string.
     * @Route("/jjalvarezl_erase_pdf", name="jjalvarezl_erase_pdf")
     *
     */
    public function deletePDF (Request $request){
        try{
            unlink(substr($request->get('PdfTmpPath'), 1));
        } catch (Exception $e){
            return new Response('Can not delete pdf file from temporal directory, '.$e->getMessage().', please configure tmpPdfDirectory and pdf variables');
        }
        return new Response('');
    }
}

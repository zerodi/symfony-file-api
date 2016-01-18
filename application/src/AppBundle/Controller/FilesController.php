<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use League\Flysystem\Filesystem;
use Symfony\Component\HttpKernel\Exception\HttpException;

class FilesController extends FOSRestController
{
    /**
     * @Route("/api/files/")
     * @Method({"GET"})
     */
    public function getFilesAction(Request $request)
    {
        $filesystem = $this->container->get('local');
        $data = ['error' => 0, 'response' => $filesystem->listPaths()];
        $view = $this->view($data, 200);
        return $this->handleView($view);
    }

    /**
     * @Route("/api/files/")
     * @Method({"POST"})
     */
    public function postFilesAction(Request $request)
    {
        $file = $request->files->get('file');
        $filesystem = $this->container->get('local');
        $stream = fopen($file->getRealPath(), 'r+');
        $filesystem->writeStream($file->getClientOriginalName(), $stream);
        fclose($stream);
        $data = ['error' => 0];
        $view = $this->view($data, 200);
        return $this->handleView($view);

    }

    /**
     * @Route("/api/files/{filename}")
     * @Method({"GET"})
     */
    public function getFileAction(Request $request, $filename)
    {
        $filesystem = $this->container->get('local');
        if (!$filesystem->has($filename))
        {
            throw new HttpException(404, "File Not Found");
        }
        $response = new Response();
        $response->headers->set('Content-Type', $filesystem->getMimetype($filename));
        $response->headers->set('Content-Disposition', 'attachment; filename="' . basename($filename) . '";');
        $response->headers->set('Content-length', $filesystem->getMimetype($filename));
        $response->sendHeaders();
        $response->setContent($filesystem->read($filename));
        return $response;
    }

    /**
     * @Route("/api/files/{filename}/meta")
     * @Method({"GET"})
     */
    public function fileMetaAction(Request $request, $filename)
    {
        $filesystem = $this->container->get('local');
        if (!$filesystem->has($filename))
        {
            throw new HttpException(404, "File Not Found");
        }
        $data = ['error' => 0, 'response' => $filesystem->getMetadata($filename)];
        $view = $this->view($data, 200);
        return $this->handleView($view);
    }

    /**
     * @Route("/api/files/{filename}")
     * @Method({"POST"})
     */
    public function putFilesAction(Request $request, $filename)
    {
        $filesystem = $this->container->get('local');
        if (!$filesystem->has($filename))
        {
            throw new HttpException(404, "File Not Found");
        }

        $file = $request->files->get('file');
        $stream = fopen($file->getRealPath(), 'r+');
        $filesystem->putStream($filename, $stream);
        fclose($stream);
        $data = ['error' => 0];
        $view = $this->view($data, 200);
        return $this->handleView($view);
    }
}

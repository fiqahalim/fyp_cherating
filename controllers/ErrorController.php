<?php

class ErrorController extends Controller
{
    public function notFound($uri = null)
    {
        http_response_code(404);
        $data = ['uri' => $uri];
        $this->view('errors/404', $data);
    }

    public function serverError($exception)
    {
        http_response_code(500);
        $data = ['e' => $exception];
        $this->view('errors/500', $data);
    }
}
<?php

namespace Src\Controllers;

session_start();

class HomeController
{
    public function index() {
        include __DIR__ . '/../../public/index.html';
        exit;
    }
}
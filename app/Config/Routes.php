<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/mahasiswa', 'Mahasiswa::index');
$routes->get('/mahasiswa/getData', 'Mahasiswa::getData');
$routes->post('/mahasiswa/simpan', 'Mahasiswa::simpan');
$routes->get('/mahasiswa/hapus/(:num)', 'Mahasiswa::hapus/$1');
